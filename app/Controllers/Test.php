<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use Config\Database;

use App\Controllers\BaseController;
use App\Models\EmailQueueModel;


class Test extends Controller
{
    public function dbcheck()
    {
        try {
            $db = Database::connect();
            $query = $db->query("SELECT DATABASE() AS dbname");
            $row = $query->getRow();
            return "✅ Connected to database: " . $row->dbname;
        } catch (\Throwable $e) {
            return "❌ DB connection failed: " . $e->getMessage();
        }
    }

    public function pwhash()
    {
        return $passwordHash = password_hash('aaaaaaaa', PASSWORD_DEFAULT);
    }

    public function email()
    {
        $queue = new EmailQueueModel();

        $id = $queue->enqueue([
            'to_email'  => 'test@example.com',
            'to_name'   => 'Test User',
            'subject'   => 'Test LCNL Email',
            'body_html' => '<p>This is a <b>test</b> email.</p>',
            'body_text' => "This is a test email.",
            'priority'  => 1,
        ]);

        return "Inserted email queue row ID: " . $id;
    }

}
