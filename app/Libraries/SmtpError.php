<?php

namespace App\Libraries;

/**
 * Turns CodeIgniter's email debug output into the one line worth keeping
 * on the queue row (email_queue.last_error), and decides whether that
 * failure is worth retrying.
 *
 * Summaries look like:
 *   "554 5.7.1 Spam message rejected"   the server's reply, code first
 *   "AUTH: Failed to authenticate …"     couldn't log in to the server
 *   "CONNECT: Unable to connect …"       couldn't reach the server
 */
final class SmtpError
{
    /**
     * @param string $debug Email::printDebugger([]) after a failed send
     */
    public static function summarise(string $debug): string
    {
        // Messages are joined with <br>, so turn those into line breaks first.
        $text = preg_replace('#<br\s*/?>#i', "\n", $debug) ?? $debug;
        $text = trim(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        // The server refused the message, sender or recipient: keep its reply.
        if (preg_match('/SMTP error was encountered:\s*([2-5]\d\d\b[^\r\n]*)/i', $text, $m)) {
            return self::clip($m[1]);
        }

        // Same wording, but an operating-system error number (e.g. "111
        // Connection refused"): the server couldn't be reached at all.
        if (preg_match('/SMTP error was encountered:\s*(\d+\s[^\r\n]*)/i', $text, $m)) {
            return self::clip('CONNECT: ' . $m[1]);
        }

        if (preg_match('/((?:Unable to initiate AUTH|Failed to authenticate|Failed to send AUTH|SMTP authentication)[^\r\n]*)/i', $text, $m)) {
            return self::clip('AUTH: ' . $m[1]);
        }

        if (preg_match('/(Unable to connect[^\r\n]*|SMTP Error: Unable to connect[^\r\n]*)/i', $text, $m)) {
            return self::clip('CONNECT: ' . $m[1]);
        }

        // Anything else: the first line that says something.
        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            $line = trim($line);
            if ($line !== ''
                && !preg_match('/^(\d{3}[ -]|hello:|from:|to:|data:|quit:)/i', $line)
                && stripos($line, 'Unable to send email using SMTP') === false
            ) {
                return self::clip($line);
            }
        }

        return 'SMTP send failed';
    }

    /**
     * A 5xx reply to the message itself (spam rejected, no such mailbox)
     * will be refused again, so retrying only repeats it, and repeated spam
     * rejections can get the mailbox blocked. Login and connection problems
     * are usually temporary.
     */
    public static function isPermanent(?string $lastError): bool
    {
        return $lastError !== null && preg_match('/^5\d\d\s/', $lastError) === 1;
    }

    private static function clip(string $line): string
    {
        return mb_substr(trim(preg_replace('/\s+/', ' ', $line)), 0, 300);
    }
}
