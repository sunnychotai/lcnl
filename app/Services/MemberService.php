<?php

namespace App\Services;

use App\Models\MemberModel;
use CodeIgniter\Database\Exceptions\DataException;
use CodeIgniter\I18n\Time;

class MemberService
{
    public function __construct(private MemberModel $members = new MemberModel()) {}

    /**
     * Create a pending member from validated input.
     */
    public function createPending(array $input): int
    {
        $data = [
            'first_name'    => trim($input['first_name']),
            'last_name'     => trim($input['last_name']),
            'email'         => strtolower(trim($input['email'])),
            'mobile'        => $input['mobile'] !== '' ? $input['mobile'] : null,
            'postcode'      => $input['postcode'] !== '' ? $input['postcode'] : null,
            'password_hash' => password_hash($input['password'], PASSWORD_DEFAULT),
            'status'        => 'pending',
            'consent_at'    => date('Y-m-d H:i:s'),
            'source'        => $input['source'] ?? 'web',
        ];

        if (! $this->members->insert($data, true)) {
            throw new DataException('Unable to create member.');
        }

        return (int) $this->members->getInsertID();
    }

    /**
     * Activate a member (mark as verified & active).
     * Also records who verified and when.
     */
    public function activate(int $memberId, ?int $adminId = null): bool
    {
        return (bool) $this->members->update($memberId, [
            'status'      => 'active',
            'verified_at' => Time::now()->toDateTimeString(),
            'verified_by' => $adminId,
            'updated_at'  => Time::now()->toDateTimeString(),
        ]);
    }

    /**
     * Disable a member (soft deactivate, keeps audit).
     */
    public function disable(int $memberId): bool
    {
        return (bool) $this->members->update($memberId, [
            'status'     => 'disabled',
            'updated_at' => Time::now()->toDateTimeString(),
        ]);
    }

    /**
     * Re-enable a disabled member (reactivate without re-verifying).
     */
    public function reactivate(int $memberId, ?int $adminId = null): bool
    {
        return (bool) $this->members->update($memberId, [
            'status'      => 'active',
            'verified_by' => $adminId,
            'updated_at'  => Time::now()->toDateTimeString(),
        ]);
    }

    /**
     * Future-ready placeholder: resend verification or welcome email.
     */
    public function resendVerification(int $memberId): bool
    {
        log_message('info', "MemberService: Resend verification email for member #{$memberId}");
        return true;
    }

    /**
     * List members by status & query.
     */
    public function list(string $status = 'pending', ?string $q = null, int $limit = 50): array
    {
        return $this->members->listByStatus($status, $q, $limit);
    }

    /**
     * Counts of members by status.
     */
    public function counts(): array
    {
        return $this->members->countsByStatus();
    }

    /**
     * Optional: Bulk recertify expired members (future yearly use)
     */
    public function recertifyExpired(?int $adminId = null): int
    {
        // Example logic placeholder — only run when business rules are ready
        // e.g. reactivate members disabled more than 30 days ago
        $builder = $this->members->builder();
        $builder->where('status', 'disabled')
            ->where('updated_at <', Time::now()->subDays(30)->toDateTimeString());

        $members = $builder->get()->getResultArray();
        $count = 0;

        foreach ($members as $m) {
            $this->activate($m['id'], $adminId);
            $count++;
        }

        return $count;
    }
}
