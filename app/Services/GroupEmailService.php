<?php

namespace App\Services;

use App\Libraries\GroupEmail\Audience;
use App\Libraries\GroupEmail\GroupEmailException;
use App\Libraries\GroupEmail\PlaceholderRenderer;
use App\Models\EmailQueueModel;
use App\Models\GroupEmailModel;
use Config\GroupEmail;

/**
 * Write once, send to a group: drafts, previews, test copies and queuing.
 *
 * Nothing is sent from here. Each recipient's copy is rendered (placeholders
 * filled, wrapped in the LCNL layout) and added to email_queue, which
 * emails:send works through at the Fasthosts-safe rate. Rendering up front
 * means the queue holds exactly what each person receives, and keeps
 * related_id free (emails:send reads that as a member id, and most
 * recipients here aren't members).
 */
class GroupEmailService
{
    public const TYPE      = 'group_email';
    public const TYPE_TEST = 'group_email_test';

    private GroupEmail $config;
    private GroupEmailModel $groupEmails;
    private EmailQueueModel $queue;

    public function __construct(?GroupEmail $config = null)
    {
        $this->config      = $config ?? config('GroupEmail');
        $this->groupEmails = new GroupEmailModel();
        $this->queue       = new EmailQueueModel();
    }

    /* ---------------------------------------------------------
     * Audiences
     * --------------------------------------------------------- */

    public function audience(string $key, ?int $ref, array $filter = []): ?Audience
    {
        $class = $this->config->audiences[$key] ?? null;

        return $class ? $class::make($ref, $filter) : null;
    }

    public function audienceFor(array $groupEmail): ?Audience
    {
        return $this->audience(
            $groupEmail['audience'],
            $groupEmail['audience_ref'] !== null ? (int) $groupEmail['audience_ref'] : null,
            json_decode((string) $groupEmail['filter_json'], true) ?: [],
        );
    }

    /* ---------------------------------------------------------
     * Rendering
     * --------------------------------------------------------- */

    /**
     * One recipient's copy.
     *
     * @return array{subject: string, html: string, text: string}
     */
    public function render(string $subject, string $body, bool $wrap, array $vars): array
    {
        $vars += ['current_year' => date('Y')];

        $subject = trim(PlaceholderRenderer::render($subject, $vars, false));
        $content = PlaceholderRenderer::render($this->stripScripts($body), $vars, true);

        if ($wrap) {
            $html = view('emails/group_email', ['title' => $subject, 'content' => $content]);
        } elseif (stripos($content, '<html') !== false) {
            $html = $content; // a complete document, sent as written
        } else {
            $html = "<!DOCTYPE html>\n<html lang=\"en\"><head><meta charset=\"UTF-8\"></head><body>"
                . $content . '</body></html>';
        }

        return ['subject' => $subject, 'html' => $html, 'text' => $this->htmlToText($html)];
    }

    /**
     * What the compose page shows: the email as the first recipient will get
     * it, who it goes to, and anything worth fixing before it's sent.
     */
    public function preview(Audience $audience, string $subject, string $body, bool $wrap): array
    {
        $list   = $audience->recipients();
        $sample = $list->first();
        $copy   = $this->render($subject, $body, $wrap, $sample['vars'] ?? $audience->sampleVars());

        return [
            'subject'    => $copy['subject'],
            'html'       => $copy['html'],
            'sample'     => $sample ? $sample['email'] : null,
            'recipients' => array_map(static fn ($r) => [
                'email' => $r['email'],
                'name'  => $r['name'],
                'party' => $r['vars']['party'] ?? null,
            ], $list->recipients),
            'skipped'    => $list->skipped,
            'warnings'   => $this->warnings($audience, $subject, $body),
            'estimate'   => $this->deliveryEstimate($list->count()),
        ];
    }

    /**
     * Problems that don't stop a send but probably should be fixed.
     *
     * @return list<string>
     */
    public function warnings(Audience $audience, string $subject, string $body): array
    {
        $warnings = [];
        $known    = array_merge(array_keys($audience->placeholders()), ['current_year']);

        $unknown = PlaceholderRenderer::unknown($subject . ' ' . $body, $known);
        if ($unknown) {
            $warnings[] = 'Not a known placeholder, so it will be sent as written: ' . implode(', ', $unknown) . '.';
        }

        if (preg_match('/<img[^>]+src\s*=\s*["\']?\s*data:/i', $body)) {
            $warnings[] = 'An image is pasted in as data. Many email apps block these; use an image hosted on a website instead.';
        }

        if (preg_match('/<img[^>]+src\s*=\s*["\']?\s*(?!https?:|data:|cid:)[^"\'\s>]/i', $body)) {
            $warnings[] = 'An image has a relative address. Email images need a full https:// address.';
        }

        if (preg_match('/<script\b/i', $body)) {
            $warnings[] = 'Scripts are removed: email apps won\'t run them.';
        }

        return $warnings;
    }

    /**
     * Rough time until the last copy goes, given what's already waiting at
     * the same or higher priority.
     */
    public function deliveryEstimate(int $count): array
    {
        $ahead = (int) $this->queue
            ->where('status', 'pending')
            ->where('priority <=', $this->config->priority)
            ->countAllResults();

        $total   = $ahead + $count;
        $minutes = (int) (ceil($total / max(1, $this->config->sendsPerTenMinutes)) * 10);

        return [
            'ahead'     => $ahead,
            'minutes'   => $count > 0 ? $minutes : 0,
            'over_day'  => $total > $this->config->sendsPerDay,
        ];
    }

    /* ---------------------------------------------------------
     * Drafts
     * --------------------------------------------------------- */

    /**
     * @return list<string> Problems with the content, empty if it's fine.
     */
    public function validate(string $subject, string $body): array
    {
        $errors = [];

        if (trim($subject) === '') {
            $errors[] = 'Add a subject.';
        } elseif (mb_strlen($subject) > 200) {
            $errors[] = 'Keep the subject under 200 characters.';
        }

        $hasText = trim(html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8')) !== '';
        if (!$hasText && !preg_match('/<img\b/i', $body)) {
            $errors[] = 'Add the email\'s content.';
        }

        return $errors;
    }

    /**
     * Create or update a draft. A draft that has already been queued can't
     * change (this is what stops a resubmitted form queuing twice).
     *
     * @param array{subject: string, body_html: string, wrap_layout: bool} $content
     * @param array{id: ?int, name: ?string} $admin
     */
    public function saveDraft(?int $id, Audience $audience, string $audienceKey, array $content, array $admin): int
    {
        $errors = $this->validate($content['subject'], $content['body_html']);
        if ($errors) {
            throw new GroupEmailException(implode(' ', $errors));
        }

        $data = [
            'audience'       => $audienceKey,
            'audience_ref'   => $audience->ref(),
            'audience_label' => mb_substr($audience->label(), 0, 255),
            'filter_json'    => json_encode($audience->filter()),
            'subject'        => trim($content['subject']),
            'body_html'      => $content['body_html'],
            'wrap_layout'    => $content['wrap_layout'] ? 1 : 0,
            'reply_to'       => $this->config->replyTo,
        ];

        if ($id) {
            $existing = $this->groupEmails->find($id);

            if ($existing && $existing['audience'] === $audienceKey && (int) $existing['audience_ref'] === $audience->ref()) {
                if ($existing['status'] !== 'draft') {
                    throw new GroupEmailException('This email has already been queued. Start a new one to send again.');
                }

                $this->groupEmails->update($id, $data);

                return $id;
            }
        }

        return (int) $this->groupEmails->insert($data + [
            'status'          => 'draft',
            'created_by'      => $admin['id'] ?: null,
            'created_by_name' => $admin['name'],
        ], true);
    }

    /* ---------------------------------------------------------
     * Sending
     * --------------------------------------------------------- */

    /**
     * Queue one copy to $toEmail, filled in with the first recipient's details
     * (or sample ones), subject prefixed [TEST]. Returns the queue row id.
     */
    public function sendTest(int $id, string $toEmail): int
    {
        $toEmail = trim($toEmail);
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            throw new GroupEmailException('Enter a valid email address for the test.');
        }

        [$groupEmail, $audience] = $this->load($id);

        $sample = $audience->recipients()->first();
        $copy   = $this->render(
            $groupEmail['subject'],
            (string) $groupEmail['body_html'],
            (bool) $groupEmail['wrap_layout'],
            $sample['vars'] ?? $audience->sampleVars(),
        );

        $queueId = $this->queue->enqueue($this->queueRow($groupEmail, $toEmail, null, $copy) + [
            'subject'  => mb_substr('[TEST] ' . $copy['subject'], 0, 255),
            'type'     => self::TYPE_TEST,
            'priority' => $this->config->testPriority,
        ]);

        $this->groupEmails->update($id, [
            'test_count'   => (int) $groupEmail['test_count'] + 1,
            'last_test_to' => $toEmail,
            'last_test_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $queueId;
    }

    /**
     * Queue a copy for every recipient, once. Returns how many were queued.
     */
    public function queue(int $id): int
    {
        [$groupEmail, $audience] = $this->load($id);

        if ($groupEmail['status'] !== 'draft') {
            throw new GroupEmailException('This email has already been queued.');
        }

        $list = $audience->recipients();
        if ($list->count() === 0) {
            throw new GroupEmailException('There is no one to send this to.');
        }

        $db  = db_connect();
        $now = date('Y-m-d H:i:s');

        $db->transBegin();

        try {
            // Claim the draft first: if two requests race, only one gets here.
            $db->table('group_emails')
                ->where('id', $id)
                ->where('status', 'draft')
                ->update(['status' => 'queued', 'queued_at' => $now, 'updated_at' => $now]);

            if ($db->affectedRows() !== 1) {
                throw new GroupEmailException('This email has already been queued.');
            }

            $rows = [];
            foreach ($list->recipients as $recipient) {
                $copy   = $this->render(
                    $groupEmail['subject'],
                    (string) $groupEmail['body_html'],
                    (bool) $groupEmail['wrap_layout'],
                    $recipient['vars'],
                );
                $rows[] = $this->queueRow($groupEmail, $recipient['email'], $recipient['name'], $copy) + [
                    'subject'    => mb_substr($copy['subject'], 0, 255),
                    'type'       => self::TYPE,
                    'priority'   => $this->config->priority,
                    'status'     => 'pending',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($rows) === 100) {
                    $db->table('email_queue')->insertBatch($rows);
                    $rows = [];
                }
            }
            if ($rows) {
                $db->table('email_queue')->insertBatch($rows);
            }

            $db->table('group_emails')->where('id', $id)->update([
                'recipient_count' => $list->count(),
                'skipped_count'   => count($list->skipped),
            ]);

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database error while queuing.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }

        return $list->count();
    }

    /**
     * Take copies that haven't gone yet out of the queue. Returns how many.
     */
    public function cancel(int $id): int
    {
        $groupEmail = $this->groupEmails->find($id);

        if (!$groupEmail || !in_array($groupEmail['status'], ['queued', 'cancelled'], true)) {
            throw new GroupEmailException('Only a queued email can be cancelled.');
        }

        $db = db_connect();
        $db->table('email_queue')
            ->where('group_email_id', $id)
            ->where('type', self::TYPE)
            ->where('status', 'pending')
            ->delete();
        $removed = $db->affectedRows();

        if ($removed > 0) {
            $this->groupEmails->update($id, [
                'status'          => 'cancelled',
                'cancelled_count' => (int) $groupEmail['cancelled_count'] + $removed,
                'cancelled_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return $removed;
    }

    /**
     * Queue rows by status for each group email.
     *
     * @param list<int> $ids
     * @return array<int, array{pending: int, sending: int, sent: int, failed: int}>
     */
    public function progress(array $ids): array
    {
        $out = [];
        foreach ($ids as $id) {
            $out[(int) $id] = ['pending' => 0, 'sending' => 0, 'sent' => 0, 'failed' => 0];
        }

        if ($ids === []) {
            return $out;
        }

        $rows = db_connect()->table('email_queue')
            ->select('group_email_id, status, COUNT(*) AS n')
            ->whereIn('group_email_id', $ids)
            ->where('type', self::TYPE)
            ->groupBy(['group_email_id', 'status'])
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $out[(int) $row['group_email_id']][$row['status']] = (int) $row['n'];
        }

        return $out;
    }

    /* ---------------------------------------------------------
     * Internals
     * --------------------------------------------------------- */

    /** @return array{0: array, 1: Audience} */
    private function load(int $id): array
    {
        $groupEmail = $this->groupEmails->find($id);
        if (!$groupEmail) {
            throw new GroupEmailException('Group email not found.');
        }

        $audience = $this->audienceFor($groupEmail);
        if (!$audience) {
            throw new GroupEmailException('The people this email was for can no longer be found.');
        }

        return [$groupEmail, $audience];
    }

    private function queueRow(array $groupEmail, string $email, ?string $name, array $copy): array
    {
        return [
            'to_email'       => $email,
            'to_name'        => $name !== '' ? $name : null,
            'group_email_id' => (int) $groupEmail['id'],
            'body_html'      => $copy['html'],
            'body_text'      => $copy['text'],
            'headers_json'   => $groupEmail['reply_to'] ? json_encode(['Reply-To' => $groupEmail['reply_to']]) : null,
        ];
    }

    private function stripScripts(string $html): string
    {
        return preg_replace('#<script\b[^>]*>.*?</script\s*>#is', '', $html) ?? $html;
    }

    /** A readable plain-text version for email apps that don't show HTML. */
    private function htmlToText(string $html): string
    {
        $text = preg_replace('#<(head|style|script)\b[^>]*>.*?</\1\s*>#is', '', $html) ?? $html;
        $text = preg_replace('#<a\b[^>]*href\s*=\s*["\'](https?://[^"\']+)["\'][^>]*>(.*?)</a>#is', '$2 ($1)', $text) ?? $text;
        $text = preg_replace('#<br\s*/?>#i', "\n", $text) ?? $text;
        $text = preg_replace('#</(p|div|h[1-6]|li|tr|table|blockquote)>#i', "\n\n", $text) ?? $text;
        $text = preg_replace('#<li\b[^>]*>#i', '• ', $text) ?? $text;
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/[ \t\x{00A0}]+/u", ' ', $text) ?? $text;
        $text = preg_replace("/ *\n */", "\n", $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }
}
