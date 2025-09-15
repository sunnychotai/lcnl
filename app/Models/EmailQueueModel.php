<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Models\EmailQueueModel;

$queue = new EmailQueueModel();

// Build your activation link (token generation not shown here)
$activationUrl = base_url('membership/verify/'.$token);

// Simple HTML/text bodies
$bodyHtml = view('emails/member_activation_html', [
    'first_name'    => $member['first_name'],
    'activationUrl' => $activationUrl,
]);

$bodyText = "Hi {$member['first_name']},\n\n".
            "Please activate your LCNL account:\n{$activationUrl}\n\n".
            "If you didn’t create this account, ignore this email.\n— LCNL";

$queue->enqueue([
    'to_email'  => $member['email'],
    'to_name'   => trim(($member['first_name'] ?? '').' '.($member['last_name'] ?? '')),
    'subject'   => 'Activate your LCNL account',
    'body_html' => $bodyHtml,
    'body_text' => $bodyText,
    'priority'  => 2, // higher priority
    // 'scheduled_at' => date('Y-m-d H:i:s', time()+300), // optional: delay 5 mins
]);

