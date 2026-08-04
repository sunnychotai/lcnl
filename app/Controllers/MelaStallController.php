<?php

namespace App\Controllers;

use App\Models\EmailQueueModel;
use App\Models\MelaStallBookingModel;
use App\Models\MelaStallDocumentModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Files\UploadedFile;
use Config\MelaStalls;

/**
 * Stall holder bookings for the Golden Jubilee Mela.
 *
 * The public page sits on an unlisted URL (see Config\MelaStalls::$slug), is
 * sent with noindex and is linked from nowhere, so it reaches only people the
 * organisers have spoken to. That is obscurity rather than authentication —
 * anyone the link is forwarded to can submit — so every booking still has to be
 * reviewed, and payment is verified separately against the bank statement.
 */
class MelaStallController extends BaseController
{
    protected MelaStalls $config;
    protected MelaStallBookingModel $bookings;
    protected MelaStallDocumentModel $documents;
    protected EmailQueueModel $emails;

    public function __construct()
    {
        $this->config    = config('MelaStalls');
        $this->bookings  = new MelaStallBookingModel();
        $this->documents = new MelaStallDocumentModel();
        $this->emails    = new EmailQueueModel();
    }

    public function form()
    {
        return view('mela/stall_form', $this->pageData());
    }

    public function submit()
    {
        // Closed is closed: never accept a booking after the deadline, and never
        // for an event that has already happened.
        if (! $this->isOpen()) {
            return redirect()->to($this->url())->with('error', $this->closedMessage());
        }

        $post = $this->request->getPost();

        // Honeypot: the field is hidden from people, so anything in it is a bot.
        // Answer as though it succeeded rather than showing an error, which
        // tells a scripted submitter nothing useful.
        if (trim((string) ($post['website'] ?? '')) !== '') {
            log_message('info', 'Mela stalls: honeypot triggered from {ip}', [
                'ip' => $this->request->getIPAddress(),
            ]);

            return redirect()->to($this->url())->with('error',
                'Thank you — if your booking was received you will get a confirmation email shortly.');
        }

        $rules = [
            'company_name'      => 'required|trim|min_length[2]|max_length[200]',
            'category'          => 'required|in_list[' . implode(',', array_keys($this->config->categories)) . ']',
            'items_description' => 'required|trim|min_length[5]|max_length[2000]',
            'contact_name'      => 'required|trim|min_length[2]|max_length[150]',
            'contact_phone'     => 'required|trim|min_length[7]|max_length[30]',
            'contact_email'     => 'required|trim|valid_email|max_length[255]',
            'vehicle_reg'       => 'permit_empty|trim|max_length[20]',
            'comments'          => 'permit_empty|trim|max_length[2000]',
            'confirmed_payment' => 'required',
            'agreed_terms'      => 'required',
        ];

        $messages = [
            'confirmed_payment' => ['required' => 'Please confirm you have paid the stall fee.'],
            'agreed_terms'      => ['required' => 'Please confirm you accept the terms.'],
            'category'          => ['required' => 'Please choose the type of stall.'],
        ];

        // "Other" is only meaningful with a description of what it is.
        if (($post['category'] ?? '') === 'other') {
            $rules['category_other'] = 'required|trim|min_length[2]|max_length[200]';
            $messages['category_other'] = ['required' => 'Please tell us what type of stall this is.'];
        }

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $isFoodStall = ($post['category'] ?? '') === 'food' || ! empty($post['is_food_stall']);

        // Validate the uploads before writing anything, so a rejected file does
        // not leave a half-saved booking behind.
        try {
            $files = $this->validDocuments();
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()
                ->with('errors', ['documents' => $e->getMessage()]);
        }

        // A food stall must supply its hygiene certificate.
        if ($isFoodStall && $files === []) {
            return redirect()->back()->withInput()->with('errors', [
                'documents' => 'Food stalls must upload a current Food Hygiene Certificate.',
            ]);
        }

        if ($this->isFull()) {
            return redirect()->to($this->url())->with('error',
                'All stalls have now been allocated. Please contact the organisers.');
        }

        $ref = $this->bookings->generateRef();

        $bookingId = $this->bookings->insert([
            'booking_ref'       => $ref,
            'company_name'      => $post['company_name'],
            'category'          => $post['category'],
            'category_other'    => $post['category'] === 'other' ? ($post['category_other'] ?? null) : null,
            'is_food_stall'     => $isFoodStall ? 1 : 0,
            'items_description' => $post['items_description'],
            'contact_name'      => $post['contact_name'],
            'contact_phone'     => $post['contact_phone'],
            'contact_email'     => trim($post['contact_email']),
            'vehicle_reg'       => $post['vehicle_reg'] ?? null,
            'comments'          => $post['comments'] ?? null,
            'confirmed_payment' => 1,
            'agreed_terms'      => 1,
            'status'            => 'submitted',
            'ip_address'        => $this->request->getIPAddress(),
        ], true);

        $this->storeDocuments((int) $bookingId, $files);

        $booking = $this->bookings->find($bookingId);

        $this->sendConfirmation($booking);
        $this->notifyOrganisers($booking, count($files));

        return redirect()->to($this->url('confirmation/' . $ref));
    }

    public function confirmation(string $ref)
    {
        $booking = $this->bookings->where('booking_ref', $ref)->first();

        if (! $booking) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('mela/stall_confirmation', $this->pageData([
            'title'   => 'Stall booking received',
            'booking' => $booking,
        ]));
    }

    /* =====================================================================
       Uploads
    ===================================================================== */

    /**
     * Collect and validate the uploaded documents.
     *
     * @return UploadedFile[]
     * @throws \RuntimeException with a message safe to show the user
     */
    private function validDocuments(): array
    {
        $files = $this->request->getFileMultiple('documents') ?? [];
        $valid = [];

        foreach ($files as $file) {
            if (! $file->isValid()) {
                // An empty file input is normal, not an error.
                if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                throw new \RuntimeException($file->getErrorString());
            }

            if (count($valid) >= $this->config->maxDocuments) {
                throw new \RuntimeException(
                    'Please upload no more than ' . $this->config->maxDocuments . ' documents.'
                );
            }

            $ext  = strtolower($file->getClientExtension());
            $mime = $file->getMimeType();

            if (! in_array($ext, $this->config->allowedDocumentExtensions, true)
                || ! in_array($mime, $this->config->allowedDocumentMimes, true)) {
                throw new \RuntimeException(
                    'Documents must be PDF or image files (' .
                    implode(', ', $this->config->allowedDocumentExtensions) . ').'
                );
            }

            if ($file->getSize() > $this->config->maxDocumentSizeKb * 1024) {
                throw new \RuntimeException(
                    '"' . $file->getClientName() . '" is larger than ' .
                    round($this->config->maxDocumentSizeKb / 1024) . 'MB.'
                );
            }

            $valid[] = $file;
        }

        return $valid;
    }

    /**
     * Move uploads to a non-web-reachable directory under a randomised name.
     * The originals are hygiene certificates carrying personal details, so they
     * must never sit on a guessable public URL.
     *
     * @param UploadedFile[] $files
     */
    private function storeDocuments(int $bookingId, array $files): void
    {
        if ($files === []) {
            return;
        }

        $dir = $this->config->documentPath();

        if (! is_dir($dir) && ! mkdir($dir, 0770, true) && ! is_dir($dir)) {
            log_message('error', 'Mela stalls: could not create upload directory {dir}', ['dir' => $dir]);

            return;
        }

        foreach ($files as $file) {
            $stored = bin2hex(random_bytes(16)) . '.' . strtolower($file->getClientExtension());

            try {
                $file->move($dir, $stored);
            } catch (\Throwable $e) {
                log_message('error', 'Mela stalls: upload failed for booking {id}: {msg}', [
                    'id'  => $bookingId,
                    'msg' => $e->getMessage(),
                ]);

                continue;
            }

            $this->documents->insert([
                'booking_id'    => $bookingId,
                'original_name' => $file->getClientName(),
                'stored_name'   => $stored,
                'mime_type'     => $file->getClientMimeType(),
                'size_bytes'    => filesize($dir . $stored) ?: 0,
            ]);
        }
    }

    /* =====================================================================
       Email
    ===================================================================== */

    private function sendConfirmation(array $booking): void
    {
        $html = view('emails/mela_stall_confirmation', [
            'booking' => $booking,
            'config'  => $this->config,
        ]);

        $this->emails->enqueue([
            'to_email'   => $booking['contact_email'],
            'to_name'    => $booking['contact_name'],
            'subject'    => 'Stall booking received – LCNL Golden Jubilee Mela',
            'type'       => 'mela_stall_confirmation',
            'related_id' => $booking['id'],
            'body_html'  => $html,
            'body_text'  => strip_tags($html),
            'priority'   => 1,
        ]);
    }

    private function notifyOrganisers(array $booking, int $documentCount): void
    {
        $recipients = $this->config->notifyEmails;

        if ($recipients === []) {
            // Not fatal: the booking is saved and visible in the admin screen.
            log_message('warning',
                'Mela stalls: booking {ref} saved but mela.stalls.notifyEmails is empty, so nobody was notified.',
                ['ref' => $booking['booking_ref']]
            );

            return;
        }

        $html = view('emails/mela_stall_notification', [
            'booking'       => $booking,
            'documentCount' => $documentCount,
            'config'        => $this->config,
        ]);

        foreach ($recipients as $email) {
            $this->emails->enqueue([
                'to_email'   => $email,
                'to_name'    => 'LCNL Mela Team',
                'subject'    => 'New Mela stall booking – ' . $booking['company_name'],
                'type'       => 'mela_stall_notification',
                'related_id' => $booking['id'],
                'body_html'  => $html,
                'body_text'  => strip_tags($html),
                'priority'   => 1,
            ]);
        }
    }

    /* =====================================================================
       Helpers
    ===================================================================== */

    private function url(string $suffix = ''): string
    {
        return site_url($this->config->slug . ($suffix !== '' ? '/' . $suffix : ''));
    }

    private function isOpen(): bool
    {
        return $this->config->isOpen() && ! $this->isFull();
    }

    private function isFull(): bool
    {
        return $this->config->capacity > 0
            && $this->bookings->countActive() >= $this->config->capacity;
    }

    private function closedMessage(): string
    {
        return 'Stall bookings closed at midnight on ' . $this->config->closingLabel()
            . '. Please contact the organisers if you still wish to book.';
    }

    private function pageData(array $extra = []): array
    {
        return array_merge([
            'title'          => 'Stall holder booking – Golden Jubilee Mela',
            'metaDescription' => 'Stall holder booking form for the LCNL Golden Jubilee Mela.',
            'noindex'        => true,
            'config'         => $this->config,
            'isOpen'         => $this->isOpen(),
            'isFull'         => $this->isFull(),
            'closedMessage'  => $this->closedMessage(),
        ], $extra);
    }
}
