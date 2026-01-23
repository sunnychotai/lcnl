<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EventRegistrationModel;
use App\Models\EmailQueueModel;

class EventRegistrationController extends BaseController
{
    protected $regs;
    protected $emails;

    public function __construct()
    {
        $this->regs = new EventRegistrationModel();
        $this->emails = new EmailQueueModel();
    }

    /**
     * Show registration form
     * Supports optional event slug e.g. /events/register/chopda-pujan
     */
    public function register(?string $eventSlug = null)
    {
        // Map slug → display name (DB later)
        $eventMap = [
            'maha-shivratri' => 'Maha Shivratri 2026',
        ];

        $selectedEvent = ($eventSlug && isset($eventMap[$eventSlug]))
            ? $eventMap[$eventSlug]
            : null;

        // Generate unique form token for this session
        $formToken = bin2hex(random_bytes(16));
        session()->set('event_form_token', $formToken);
        session()->set('event_form_token_time', time());

        return view('events/register', [
            'selectedEvent' => $selectedEvent,
            'isMember' => session()->get('isMemberLoggedIn') === true,
            'memberName' => session()->get('member_name'),
            'memberEmail' => session()->get('member_email'),
            'formToken' => $formToken,
        ]);
    }

    /**
     * Handle form submission with comprehensive anti-spam measures
     */
    public function submit()
    {
        // ========================================
        // ANTI-SPAM LAYER 1: Honeypot Detection
        // ========================================
        $website = $this->request->getPost('website');
        $company = $this->request->getPost('company');

        if (!empty($website) || !empty($company)) {
            // Bot detected - silently discard
            log_message('warning', 'Spam detected (honeypot): ' . $this->request->getIPAddress());
            return redirect()->to(site_url('events/thanks'));
        }

        // ========================================
        // ANTI-SPAM LAYER 2: Rate Limiting by IP
        // ========================================
        $ip = $this->request->getIPAddress();
        $rateLimitKey = 'event_registration_' . md5($ip);
        $recentSubmissions = cache($rateLimitKey) ?? [];

        // Clean old entries (older than 1 hour)
        $recentSubmissions = array_filter($recentSubmissions, function ($time) {
            return (time() - $time) < 3600;
        });

        // Check if IP has submitted more than 3 times in last hour
        if (count($recentSubmissions) >= 3) {
            log_message('warning', 'Rate limit exceeded: ' . $ip);
            return redirect()->back()
                ->withInput()
                ->with('errors', ['You have submitted too many registrations. Please try again later.']);
        }

        // ========================================
        // ANTI-SPAM LAYER 3: Form Token Validation
        // ========================================
        $submittedToken = $this->request->getPost('form_token');
        $sessionToken = session()->get('event_form_token');
        $tokenTime = session()->get('event_form_token_time');

        if (!$submittedToken || $submittedToken !== $sessionToken) {
            log_message('warning', 'Invalid form token: ' . $ip);
            return redirect()->back()
                ->withInput()
                ->with('errors', ['Invalid form submission. Please refresh the page and try again.']);
        }

        // Prevent token reuse (double submission)
        session()->remove('event_form_token');
        session()->remove('event_form_token_time');

        // ========================================
        // ANTI-SPAM LAYER 4: Time-based Validation
        // ========================================
        $formTime = (int) $this->request->getPost('form_time');
        $currentTime = time();
        $timeTaken = $currentTime - $formTime;

        // Form filled too quickly (less than 3 seconds)
        if ($timeTaken < 3) {
            log_message('warning', 'Form submitted too quickly (' . $timeTaken . 's): ' . $ip);
            return redirect()->back()
                ->withInput()
                ->with('errors', ['Please take your time to fill out the form carefully.']);
        }

        // Form filled suspiciously quickly (less than 2 seconds from token generation)
        if ($tokenTime && ($currentTime - $tokenTime) < 2) {
            log_message('warning', 'Suspicious submission speed: ' . $ip);
            return redirect()->back()
                ->withInput()
                ->with('errors', ['Form submitted too quickly. Please try again.']);
        }

        // Form took way too long (>2 hours - possible CSRF attack with old token)
        if ($tokenTime && ($currentTime - $tokenTime) > 7200) {
            log_message('warning', 'Form token expired: ' . $ip);
            return redirect()->back()
                ->withInput()
                ->with('errors', ['Your session has expired. Please refresh the page and submit again.']);
        }

        // ========================================
        // ANTI-SPAM LAYER 5: Human Confirmation
        // ========================================
        $humanConfirm = $this->request->getPost('human_confirm');
        if (!$humanConfirm || $humanConfirm !== 'on') {
            log_message('warning', 'Human confirmation not checked: ' . $ip);
            return redirect()->back()
                ->withInput()
                ->with('errors', ['Please confirm that you are a real person.']);
        }

        // ========================================
        // ANTI-SPAM LAYER 6: JavaScript Detection
        // ========================================
        $jsEnabled = $this->request->getPost('js_enabled');
        if (!$jsEnabled) {
            // Most legitimate users have JS enabled
            // Log this but don't block (some elderly users may have JS disabled)
            log_message('info', 'Form submitted without JavaScript: ' . $ip);
        }

        // ========================================
        // ANTI-SPAM LAYER 7: Field Order Validation
        // ========================================
        $fieldOrder = $this->request->getPost('field_order');
        // Expected order: 1,2,3,4,5,6,7 (or subset in order)
        // If fields are filled in completely wrong order, might be a bot
        if ($fieldOrder) {
            $orders = explode(',', $fieldOrder);
            if (count($orders) > 0) {
                // Check if order is ascending (humans typically fill forms top to bottom)
                $isOrdered = true;
                for ($i = 1; $i < count($orders); $i++) {
                    if ((int) $orders[$i] < (int) $orders[$i - 1]) {
                        $isOrdered = false;
                        break;
                    }
                }

                // If completely out of order and filled very quickly, suspicious
                if (!$isOrdered && $timeTaken < 5) {
                    log_message('warning', 'Suspicious field order: ' . $fieldOrder . ' in ' . $timeTaken . 's');
                    // Don't block, just log - some users might jump around
                }
            }
        }

        // ========================================
        // ANTI-SPAM LAYER 8: Content Analysis
        // ========================================
        $firstName = $this->request->getPost('first_name');
        $lastName = $this->request->getPost('last_name');
        $email = $this->request->getPost('email');
        $notes = $this->request->getPost('notes');

        // Check for spam patterns
        $spamPatterns = [
            '/\b(viagra|cialis|casino|poker|loan|bitcoin|crypto)\b/i',
            '/\b(click here|buy now|limited offer|act now)\b/i',
            '/http[s]?:\/\//i', // URLs in name fields
            '/\b[A-Z]{10,}\b/', // ALL CAPS SPAM
        ];

        foreach ($spamPatterns as $pattern) {
            if (preg_match($pattern, $firstName . ' ' . $lastName . ' ' . $notes)) {
                log_message('warning', 'Spam pattern detected: ' . $ip);
                return redirect()->to(site_url('events/thanks')); // Silently discard
            }
        }

        // Check for suspicious email patterns
        $suspiciousEmailPatterns = [
            '/\+test@/i',
            '/@temp/i',
            '/@disposable/i',
            '/@guerrillamail/i',
            '/@10minutemail/i',
        ];

        foreach ($suspiciousEmailPatterns as $pattern) {
            if (preg_match($pattern, $email)) {
                log_message('warning', 'Suspicious email domain: ' . $email);
                // Don't block completely, but flag
            }
        }

        // ========================================
        // STANDARD VALIDATION
        // ========================================
        $rules = [
            'event_name' => 'required|min_length[3]',
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email',
            'phone' => 'required|min_length[7]|max_length[20]',
            'num_participants' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[10]',
            'num_guests' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[10]',
            'notes' => 'permit_empty|max_length[500]',

            // ✅ NEW
            'is_lcnl_member' => 'required|in_list[0,1]',
            'agreed_terms' => 'required|in_list[1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // ========================================
        // SAVE REGISTRATION
        // ========================================
        $isMember = session()->get('isMemberLoggedIn') === true;

        $data = [
            'event_name' => $this->request->getPost('event_name'),
            'first_name' => strip_tags($firstName),
            'last_name' => strip_tags($lastName),
            'email' => $email,
            'phone' => strip_tags($this->request->getPost('phone')),
            'num_participants' => (int) $this->request->getPost('num_participants'),
            'num_guests' => (int) ($this->request->getPost('num_guests') ?? 0),
            'notes' => strip_tags($notes),
            'status' => 'submitted',
            'member_id' => $isMember ? (int) session()->get('member_id') : null,

            // ✅ NEW
            'is_lcnl_member' => (int) $this->request->getPost('is_lcnl_member'),
            'agreed_terms' => 1,

            'ip_address' => $ip, // Store IP for tracking
        ];

        // Persist registration
        $registrationId = $this->regs->insert($data);

        // Update rate limit cache
        $recentSubmissions[] = time();
        cache()->save($rateLimitKey, $recentSubmissions, 3600);

        // ========================================
        // SEND CONFIRMATION EMAILS
        // ========================================

        /* Member confirmation email */
        $memberEmailHtml = view('emails/event_registration_generic', array_merge($data, [
            'title' => 'LCNL Event Registration Received',
            'registration_id' => $registrationId,
        ]));

        $this->emails->enqueue([
            'to_email' => $data['email'],
            'to_name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'subject' => 'LCNL Event Registration Received',
            'body_html' => $memberEmailHtml,
            'body_text' => strip_tags($memberEmailHtml),
            'priority' => 1,
        ]);

        /* Admin notification email */
        $adminEmailHtml = view('emails/event_registration_admin', array_merge($data, [
            'title' => 'New Event Registration – LCNL',
            'registration_id' => $registrationId,
            'submission_time' => date('d/m/Y H:i:s'),
            'time_taken' => $timeTaken . ' seconds',
        ]));

        $this->emails->enqueue([
            'to_email' => 'info@lcnl.co.uk',
            'to_name' => 'LCNL Admin',
            'subject' => 'New Event Registration Submitted',
            'body_html' => $adminEmailHtml,
            'body_text' => strip_tags($adminEmailHtml),
            'priority' => 2,
        ]);

        // Log successful registration
        log_message('info', 'Event registration successful: ' . $email . ' (ID: ' . $registrationId . ')');

        // Flash data for thank-you page
        session()->setFlashdata([
            'first_name' => $data['first_name'],
            'event_name' => $data['event_name'],
        ]);

        return redirect()->to(site_url('events/thanks'));
    }

    /**
     * Thank-you page
     */
    public function thankyou()
    {
        return view('events/thanks');
    }
}
