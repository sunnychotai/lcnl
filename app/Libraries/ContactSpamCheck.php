<?php

namespace App\Libraries;

/**
 * Catches the spam a bot check can't: messages typed by people paid to post
 * links. The rules are deliberately cautious, so a genuine message is
 * unlikely to trip them; when one does, the sender is told what to change.
 * The language or script a message is written in is never a factor.
 */
final class ContactSpamCheck
{
    public const MAX_LINKS = 2;

    private const LINK = '~(?:https?://|www\.)\S+~i';

    /**
     * @param array{name?: string, subject?: string, message?: string} $fields
     * @return string|null What the sender should change, or null if it's fine.
     */
    public static function problem(array $fields): ?string
    {
        $name    = (string) ($fields['name'] ?? '');
        $subject = (string) ($fields['subject'] ?? '');
        $message = (string) ($fields['message'] ?? '');

        if (preg_match(self::LINK, $name . ' ' . $subject)) {
            return 'Please remove web links from your name and subject.';
        }

        if (preg_match('~\[url[=\]]|<a\s+href~i', $message)) {
            return 'Please write your message as plain text, without HTML or [url] tags.';
        }

        if (preg_match_all(self::LINK, $message) > self::MAX_LINKS) {
            return 'Please include no more than ' . self::MAX_LINKS . ' web links in your message.';
        }

        return null;
    }
}
