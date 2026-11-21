<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Change group emails through App\Services\GroupEmailService, which keeps
 * the queue rows and the counts here in step.
 */
class GroupEmailModel extends Model
{
    protected $table         = 'group_emails';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'audience',
        'audience_ref',
        'audience_label',
        'filter_json',
        'subject',
        'body_html',
        'wrap_layout',
        'reply_to',
        'status',
        'recipient_count',
        'skipped_count',
        'cancelled_count',
        'test_count',
        'last_test_to',
        'last_test_at',
        'created_by',
        'created_by_name',
        'queued_at',
        'cancelled_at',
    ];
}
