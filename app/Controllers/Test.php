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

        $subject = 'LCNL Styled Test Email';
        $bodyHtml = view('emails/test_email', [
            'name' => 'Sunny',
            'subject' => 'This is a test email - Subject',
            'ctaLink' => base_url('events'),
            'email' => 'sunnychotai@me.com',
            'message' => 'This is a test email - message'
        ]);
        $bodyText = "Hello Sunny,\n\nThis is the plain text version of the test email.\nVisit: " . base_url('events');

        $id = $queue->enqueue([
            'to_email' => 'sunnychotai@me.com',
            'to_name' => 'Sunny Chotai',
            'subject' => 'This is a test email - Subject',
            'body_html' => 'This is a test email - Body HTML',
            'body_text' => $bodyText,
            'priority' => 1,
        ]);

        return "Inserted test email queue row ID: " . $id;
    }
}
