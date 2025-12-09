<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

/**
 * Usage:
 *   php spark member:import --file=/path/to/file.csv
 *   php spark member:import --file=/path/to/file.xlsx
 *
 * Options:
 *   --dry-run        : Parse and validate but do NOT write members/memberships/family
 *   --limit=500      : Process only first N pending rows
 *   --offset=0       : Skip first N rows
 *   --batch=200      : Commit every N rows (defaults to 200)
 *   --only-staging   : Only load the file into staging table (no processing)
 *
 * CSV/XLSX expected columns (case-insensitive header match):
 *   ID, first_name, last_name, address1, address2, city, postcode, email, mobile,
 *   member_family.name, member_family.email, member_family.telephone
 */
class MemberImport extends BaseCommand
{
    protected $group = 'LCNL';
    protected $name = 'member:import';
    protected $description = 'Import Members from CSV/XLSX via staging then create Members, Member Family, and Memberships';

    protected $usage = 'member:import --file=<path> [--dry-run] [--limit=N] [--offset=N] [--batch=N] [--only-staging]';

    protected $options = [
        'file' => 'Path to CSV or XLSX file',
        'dry-run' => 'Validate only — do not write members/memberships/family',
        'limit' => 'Process first N rows',
        'offset' => 'Skip first N rows',
        'batch' => 'Commit batch size',
        'only-staging' => 'Only load file into staging table',
    ];

    protected $arguments = [];



    public function run(array $params)
    {
        CLI::write("DEBUG: MemberImport loaded successfully", 'green');

        $this->db = Database::connect();

        // Try multiple methods to get the file option
        $file = CLI::getOption('file');

        // Fallback: parse from $params array
        if (empty($file)) {
            foreach ($params as $param) {
                if (str_starts_with($param, '--file=')) {
                    $file = substr($param, 7); // Remove '--file='
                    break;
                }
            }
        }

        // Another fallback: check if file is in params without --
        if (empty($file) && isset($params['file'])) {
            $file = $params['file'];
        }

        CLI::write('FILE OPTION = ' . var_export($file, true), 'yellow');

        $dryRun = CLI::getOption('dry-run') !== null;
        $limit = (int) (CLI::getOption('limit') ?? 0);
        $offset = (int) (CLI::getOption('offset') ?? 0);
        $batchSize = (int) (CLI::getOption('batch') ?? 200);
        $onlyStaging = CLI::getOption('only-staging') !== null;

        if (empty($file) || !is_file($file)) {
            CLI::error('Provide a valid --file=/path/to/file.csv|.xlsx');
            CLI::write('Params received: ' . json_encode($params), 'yellow');
            return;
        }

        CLI::write('== LCNL Member Import ==', 'yellow');
        CLI::write('File: ' . $file);
        CLI::write('Dry run: ' . ($dryRun ? 'YES' : 'NO'));
        CLI::write('Only staging: ' . ($onlyStaging ? 'YES' : 'NO'));
        CLI::newLine();
        // 1) Load file -> staging
        $count = $this->loadIntoStaging($file);
        CLI::write("Loaded/merged into staging: ~{$count} rows.", 'green');

        if ($onlyStaging) {
            CLI::write('Only staging requested; stopping here.', 'yellow');
            return;
        }

        // 2) Process staging -> members + memberships + member_family
        $this->processStaging($dryRun, $limit, $offset, $batchSize);
    }

    /**
     * Load the CSV/XLSX into staging. For rows already in staging (matched on source_id + email + first_name + last_name),
     * we update the row values (but keep existing new_member_id/status if already processed).
     */
    protected function loadIntoStaging(string $file): int
    {
        $rows = [];

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($ext === 'xlsx') {
            if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                CLI::error('XLSX support requires "composer require phpoffice/phpspreadsheet".');
                exit(1);
            }
            $rows = $this->readXlsx($file);
        } else {
            $rows = $this->readCsv($file);
        }

        $builder = $this->db->table('member_upload_staging');

        $insertedOrMerged = 0;
        $now = date('Y-m-d H:i:s');

        foreach ($rows as $r) {
            $payload = [
                'source_id' => $r['ID'] ?? null,
                'first_name' => $r['first_name'] ?? null,
                'last_name' => $r['last_name'] ?? null,
                'address1' => $r['address1'] ?? null,
                'address2' => $r['address2'] ?? null,
                'city' => $r['city'] ?? null,
                'postcode' => $r['postcode'] ?? null,
                'email' => $r['email'] ?? null,
                'mobile' => $r['mobile'] ?? null,
                'family_name' => $r['member_family.name'] ?? null,
                'family_email' => $r['member_family.email'] ?? null,
                'family_telephone' => $r['member_family.telephone'] ?? null,
                'updated_at' => $now,
            ];

            // Upsert strategy: try to find existing staging row by a natural-ish key
            $match = $this->db->table('member_upload_staging')
                ->select('id, new_member_id, status')
                ->where('source_id', $payload['source_id'])
                ->where('email', $payload['email'])
                ->where('first_name', $payload['first_name'])
                ->where('last_name', $payload['last_name'])
                ->get()->getRowArray();

            if ($match) {
                // Don’t wipe processing fields; just update raw columns
                $this->db->table('member_upload_staging')
                    ->where('id', $match['id'])
                    ->update($payload);
                $insertedOrMerged++;
                continue;
            }

            $payload['status'] = 'pending';
            $payload['created_at'] = $now;

            $builder->insert($payload);
            $insertedOrMerged++;
        }

        return $insertedOrMerged;
    }

    protected function readCsv(string $file): array
    {
        $fp = new \SplFileObject($file);
        $fp->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);

        $rows = [];
        $headers = [];
        foreach ($fp as $i => $row) {
            if ($row === [null] || $row === false)
                continue;

            if ($i === 0) {
                $headers = array_map(fn($h) => strtolower(trim((string) $h)), $row);
                continue;
            }
            $assoc = [];
            foreach ($headers as $idx => $h) {
                $assoc[$h] = isset($row[$idx]) ? trim((string) $row[$idx]) : null;
            }
            $rows[] = $assoc;
        }
        return $rows;
    }

    protected function readXlsx(string $file): array
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
        $reader->setReadDataOnly(true);
        $ss = $reader->load($file);
        $ws = $ss->getSheet(0);

        $highestRow = $ws->getHighestRow();
        $highestCol = $ws->getHighestColumn();

        $headerRow = $ws->rangeToArray("A1:{$highestCol}1")[0] ?? [];
        $headers = array_map(fn($h) => strtolower(trim((string) $h)), $headerRow);

        $rows = [];
        for ($r = 2; $r <= $highestRow; $r++) {
            $line = $ws->rangeToArray("A{$r}:{$highestCol}{$r}")[0] ?? [];
            $assoc = [];
            foreach ($headers as $i => $h) {
                $assoc[$h] = isset($line[$i]) ? trim((string) $line[$i]) : null;
            }
            // Skip empty lines (no first/last name & no email)
            if (empty($assoc['first_name']) && empty($assoc['last_name']) && empty($assoc['email'])) {
                continue;
            }
            $rows[] = $assoc;
        }
        return $rows;
    }

    protected function processStaging(bool $dryRun, int $limit, int $offset, int $batchSize): void
    {
        $builder = $this->db->table('member_upload_staging');
        if ($limit > 0)
            $builder->limit($limit, $offset);

        $rows = $builder
            ->where('status', 'pending')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $total = count($rows);
        $done = 0;
        $ok = 0;
        $failed = 0;

        CLI::write("Processing {$total} pending rows...", 'yellow');
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        foreach ($rows as $row) {
            $done++;

            // skip if already processed (idempotency)
            if (!empty($row['new_member_id'])) {
                CLI::write("Row #{$row['id']} already has new_member_id={$row['new_member_id']} — skipping.", 'dark_gray');
                continue;
            }

            // Validate minimum viable fields
            $first = trim((string) ($row['first_name'] ?? ''));
            $last = trim((string) ($row['last_name'] ?? ''));
            $email = strtolower(trim((string) ($row['email'] ?? '')));

            if ($first === '' || $last === '') {
                $this->markError($row['id'], 'Missing first_name/last_name');
                $failed++;
                continue;
            }

            // Prepare cleaned data
            $postcode = $this->cleanPostcode((string) ($row['postcode'] ?? ''));
            $mobile = $this->cleanMobile((string) ($row['mobile'] ?? ''));
            $familyName = trim((string) ($row['family_name'] ?? ''));
            $familyEmail = strtolower(trim((string) ($row['family_email'] ?? '')));
            $familyTel = $this->cleanMobile((string) ($row['family_telephone'] ?? ''));

            // Check duplicate email rule (skip & error if member with same email exists)
            // Exception: if email ends with @lcnl.org we will override to {id}@lcnl.org anyway.
            $isLCNL = $this->isLCNLDomain($email);
            if (!$isLCNL && $email !== '') {
                if ($this->memberEmailExists($email)) {
                    $this->markError($row['id'], "Duplicate member email: {$email}");
                    $failed++;
                    continue;
                }
            }

            // Start transaction per row
            $this->db->transBegin();

            try {
                if ($dryRun) {
                    // just simulate insert id
                    $newMemberId = 999999; // fake id in dry-run
                } else {
                    // insert member
                    $memberData = [
                        'first_name' => $first,
                        'last_name' => $last,
                        'address1' => $row['address1'] ?? null,
                        'address2' => $row['address2'] ?? null,
                        'city' => $row['city'] ?? null,
                        'postcode' => $postcode,
                        'mobile' => $mobile,
                        'email' => $email ?: null,
                        'status' => 'Pending',
                        'source' => 'Upload',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    // If email is @lcnl.org we’ll set a TEMP unique email first (avoid unique constraint),
                    // then update to {id}@lcnl.org after insert.
                    $tempEmailUsed = false;
                    if ($isLCNL) {
                        $memberData['email'] = $this->makeTempLCNLEmail();
                        $tempEmailUsed = true;
                    } else {
                        // On non-LCNL but empty email: we still allow NULL
                        if ($memberData['email'] && $this->memberEmailExists($memberData['email'])) {
                            throw new \RuntimeException("Unexpected duplicate email at insert: {$memberData['email']}");
                        }
                    }

                    $this->db->table('members')->insert($memberData);
                    if ($this->db->error()['code']) {
                        CLI::error("MEMBER INSERT FAILED: " . $this->db->error()['message']);
                    }
                    $newMemberId = (int) $this->db->insertID();

                    if ($tempEmailUsed) {
                        $desired = "{$newMemberId}@lcnl.org";
                        // Just in case, ensure uniqueness (should be unique by construction)
                        if ($this->memberEmailExists($desired)) {
                            // fallback with suffix
                            $desired = "{$newMemberId}.{$this->randomSuffix()}@lcnl.org";
                        }
                        $this->db->table('members')->where('id', $newMemberId)->update(['email' => $desired, 'updated_at' => $now]);
                    }

                    // Insert membership
                    $membership = [
                        'member_id' => $newMemberId,
                        'membership_type' => 'Life',
                        'start_date' => $today,
                        'status' => 'Active',
                        'created_at' => $now,
                        'updated_at' => $now,

                    ];
                    $this->db->table('memberships')->insert($membership);
                    if ($this->db->error()['code']) {
                        CLI::error("MEMBER INSERT FAILED: " . $this->db->error()['message']);
                    }

                    // Insert member_family if valid (name required)
                    if ($familyName !== '') {
                        $familyRow = [
                            'member_id' => $newMemberId,
                            'name' => $familyName,
                            'relation' => 'Spouse', // per Q6 default
                            'email' => $familyEmail ?: null,
                            'telephone' => $familyTel ?: null,
                            'created_at' => $now,
                            'updated_at' => $now,

                        ];
                        $this->db->table('member_family')->insert($familyRow);
                        if ($this->db->error()['code']) {
                            CLI::error("MEMBER INSERT FAILED: " . $this->db->error()['message']);
                        }
                    }
                }

                // Mark staging row as imported and write back id
                if (!$dryRun) {
                    $this->db->table('member_upload_staging')->where('id', $row['id'])->update([
                        'new_member_id' => $newMemberId,
                        'status' => 'imported',
                        'processed_at' => $now,
                        'updated_at' => $now,
                        'error_message' => null,
                    ]);
                }

                $this->db->transCommit();
                $ok++;

                if ($done % $batchSize === 0) {
                    CLI::write("Progress: {$done}/{$total} (ok={$ok}, failed={$failed})", 'green');
                }
            } catch (\Throwable $e) {
                $this->db->transRollback();
                $this->markError($row['id'], $e->getMessage());
                $failed++;
            }
        }

        CLI::newLine();
        CLI::write("Done. OK={$ok}, Failed={$failed}, Total={$total}", $failed ? 'yellow' : 'green');
    }

    protected function memberEmailExists(string $email): bool
    {
        $row = $this->db->table('members')->select('id')->where('email', $email)->get()->getRowArray();
        return (bool) $row;
    }

    protected function isLCNLDomain(?string $email): bool
    {
        if (!$email)
            return false;
        return str_ends_with($email, '@lcnl.org');
    }

    protected function makeTempLCNLEmail(): string
    {
        return 'temp-' . bin2hex(random_bytes(6)) . '@lcnl.org';
    }

    protected function randomSuffix(): string
    {
        return substr(bin2hex(random_bytes(2)), 0, 4);
    }

    protected function markError(int $stagingId, string $message): void
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('member_upload_staging')
            ->where('id', $stagingId)
            ->update([
                'status' => 'error',
                'error_message' => mb_substr($message, 0, 2000),
                'processed_at' => $now,
                'updated_at' => $now,
            ]);

        CLI::error("Row #{$stagingId}: {$message}");
    }

    protected function cleanPostcode(string $pc): ?string
    {
        $pc = strtoupper(trim($pc));
        return $pc !== '' ? $pc : null;
    }

    protected function cleanMobile(string $tel): ?string
    {
        $digits = preg_replace('/\D+/', '', $tel ?? '');
        if ($digits === '')
            return null;

        // normalise UK:
        // 0044xxxxxxxxxx -> 0xxxxxxxxxx
        if (str_starts_with($digits, '0044')) {
            $digits = '0' . substr($digits, 4);
        } elseif (str_starts_with($digits, '44')) {
            $digits = '0' . substr($digits, 2);
        } elseif ($digits[0] !== '0') {
            // If leading 0 is missing, add it (per Q3)
            $digits = '0' . $digits;
        }

        // limit reasonable length
        if (strlen($digits) > 15) {
            $digits = substr($digits, 0, 15);
        }

        return $digits;
    }
}
