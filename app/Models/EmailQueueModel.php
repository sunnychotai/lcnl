<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailQueueModel extends Model
{
    protected $table            = 'email_queue';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields    = [
        'to_email',
        'to_name',
        'subject',
        'body_html',
        'body_text',
        'priority',
        'status',
        'scheduled_at',
        'sent_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Enqueue an email (array style).
     */
    public function enqueue(array $data): int
    {
        $data['status']   = $data['status']   ?? 'pending';
        $data['priority'] = $data['priority'] ?? 1;

        return $this->insert($data, true);
    }
}
