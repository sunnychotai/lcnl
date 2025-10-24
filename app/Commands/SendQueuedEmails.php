<?php

namespace App\Commands;

use App\Models\EmailQueueModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SendQueuedEmails extends BaseCommand
{
    protected $group       = 'Email';
    protected $name        = 'emails:send';
    protected $description = 'Send queued emails in batches with daily quota.';

    public function run(array $params)
    {
        $cfg      = config('EmailQueue');
        $emailCfg = config('Email');
        $batch    = (int) (CLI::getOption('batch') ?? $cfg->batchSize);
        $now      = date('Y-m-d H:i:s');

        $model    = new EmailQueueModel();

        $midnight   = date('Y-m-d 00:00:00');
        $sentToday  = $model->where('status', 'sent')
                            ->where('sent_at >=', $midnight)
                            ->countAllResults();
        $remaining  = max(0, $cfg->dailyLimit - $sentToday);
        if ($remaining <= 0) {
            CLI::write("Daily limit reached. Exiting.", 'yellow');
            return;
        }

        $take = min($batch, $remaining);

        $rows = $model->where('status', 'pending')
                      ->groupStart()
                        ->where('scheduled_at', null)
                        ->orWhere('scheduled_at <=', $now)
                      ->groupEnd()
                      ->orderBy('priority', 'ASC')
                      ->orderBy('id', 'ASC')
                      ->findAll($take);

        if (empty($rows)) {
            CLI::write("No emails to send.", 'green');
            return;
        }

        $email = \Config\Services::email();

        $processed = 0;
        $sent = 0;
        $failed = 0;

        foreach ($rows as $row) {
            $model->update($row['id'], [
                'status'   => 'sending',
                'attempts' => (int) $row['attempts'] + 1
            ]);

            try {
                if ($cfg->dryRun || ! $cfg->sendEnabled) {
                    $model->update($row['id'], [
                        'status'     => 'sent',
                        'sent_at'    => date('Y-m-d H:i:s'),
                        'last_error' => 'DRY-RUN',
                    ]);
                    $sent++;
                } else {
                    $email->clear(true);
                    $email->setFrom($emailCfg->fromEmail, $emailCfg->fromName);
                    $email->setTo($row['to_email'], $row['to_name'] ?? null);
                    $email->setSubject($row['subject']);

                    if (!empty($row['body_html'])) {
                        $email->setMessage($row['body_html']);
                    }
                    if (!empty($row['body_text'])) {
                        $email->setAltMessage($row['body_text']);
                    }

                    if (!empty($row['headers_json'])) {
                        $headers = json_decode($row['headers_json'], true) ?: [];
                        foreach ($headers as $k => $v) {
                            $email->setHeader($k, $v);
                        }
                    }

                    if ($email->send()) {
                        $model->update($row['id'], [
                            'status'     => 'sent',
                            'sent_at'    => date('Y-m-d H:i:s'),
                            'last_error' => null,
                        ]);
                        $sent++;
                    } else {
                        $err = $email->printDebugger(['headers', 'subject', 'body']);
                        $model->update($row['id'], [
                            'status'     => 'failed',
                            'last_error' => substr($err, 0, 65535),
                        ]);
                        $failed++;
                    }
                }

                $processed++;
                if ($processed >= $take) break;

            } catch (\Throwable $e) {
                $model->update($row['id'], [
                    'status'     => 'failed',
                    'last_error' => substr($e->getMessage(), 0, 65535),
                ]);
                $failed++;
            }
        }

        CLI::write("Emails processed: {$processed} | Sent: {$sent} | Failed: {$failed}", 'green');
    }
}
