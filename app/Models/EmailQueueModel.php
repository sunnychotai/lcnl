<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class EmailQueueModel extends Model
{
    protected $table = 'email_queue';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'to_email',
        'to_name',
        'subject',
        'type',
        'related_id',
        'group_email_id',
        'body_html',
        'body_text',
        'headers_json',
        'priority',
        'status',
        'attempts',
        'last_error',
        'scheduled_at',
        'sent_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Optional soft deletes
    // protected $useSoftDeletes = true;
    // protected $deletedField    = 'deleted_at';

    /**
     * Add a new email to the queue.
     */
    public function enqueue(array $data): int
    {
        $data['status'] = $data['status'] ?? 'pending';
        $data['priority'] = $data['priority'] ?? 5;

        return $this->insert($data, true);
    }

    /**
     * Failed emails worth sending again. Left out: invalid addresses, and
     * permanent rejections (a 5xx reply such as "554 5.7.1 Spam message
     * rejected", see App\Libraries\SmtpError), which would only be refused
     * again and can get the mailbox blocked for spam.
     */
    public function retryableFailed(): BaseBuilder
    {
        return $this->db->table($this->table)
            ->where('status', 'failed')
            ->groupStart()
                ->where('last_error', null)
                ->orGroupStart()
                    ->where('last_error !=', 'INVALID_EMAIL')
                    ->where("last_error NOT LIKE '5__ %'", null, false)
                ->groupEnd()
            ->groupEnd();
    }
}
