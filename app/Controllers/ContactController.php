<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmailQueueModel;

class ContactController extends BaseController
{
    public function send()
    {
        $rules = [
            'name' => 'required|min_length[2]',
            'email' => 'required|valid_email',
            'subject' => 'required|min_length[3]',
            'message' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Honeypot check (basic spam filter)
        if (!empty($this->request->getPost('website'))) {
            return redirect()->back()->with('success', 'Thank you — your message has been received.');
        }

        $payload = $this->request->getPost();

        // Prepare email body
        $bodyHtml = view('emails/contact_html', [
            'name' => $payload['name'],
            'email' => $payload['email'],
            'subject' => $payload['subject'],
            'message' => nl2br(esc($payload['message'])),
        ]);

        $bodyText = view('emails/contact_text', [
            'name' => $payload['name'],
            'email' => $payload['email'],
            'subject' => $payload['subject'],
            'message' => $payload['message'],
        ]);

        $queue = new EmailQueueModel();


        foreach (['info@lcnl.co.uk', 'info@lcnl.org'] as $recipient) {
            $queue->enqueue([
                'to_email' => $recipient,
                'to_name' => 'LCNL Admin',
                'subject' => 'LCNL Website Contact Form',
                'body_html' => $bodyHtml,
                'body_text' => $bodyText,
                'priority' => 1,
                'headers_json' => json_encode([
                    'Reply-To' => $payload['email']
                ])
            ]);
        }

        return redirect()->back()->with('success', 'Your message has been sent successfully. We’ll reply soon.');
    }
}
