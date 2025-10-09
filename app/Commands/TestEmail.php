<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestEmail extends BaseCommand
{
    protected $group       = 'Email';
    protected $name        = 'email:test';
    protected $description = 'Test email configuration with Mailpit';

    public function run(array $params)
    {
        $email = service('email');

        $email->setTo('test@example.com');
        $email->setFrom('no-reply@lcnl.local', 'LCNL');
        $email->setSubject('Test Mail from CLI');
        $email->setMessage('<p>Hello world from LCNL test.</p>');

        if ($email->send()) {
            CLI::write('✅ Email sent successfully to Mailpit', 'green');
            CLI::write('Check Mailpit at: http://localhost:8025', 'yellow');
        } else {
            CLI::error('❌ Failed to send email:');
            CLI::write($email->printDebugger(['headers', 'subject', 'body']));
        }
    }
}