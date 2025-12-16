<?php

namespace App\Models;

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
}
