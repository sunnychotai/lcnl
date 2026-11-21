<?php

namespace Config;

use App\Libraries\GroupEmail\EventRegistrationAudience;
use CodeIgniter\Config\BaseConfig;

/**
 * Group emails written in the admin and sent through the email queue.
 *
 * To email a new group (all active members, Mehfil bookers…), write a class
 * implementing App\Libraries\GroupEmail\Audience and register it here.
 */
class GroupEmail extends BaseConfig
{
    /**
     * Audience key (used in URLs and stored on group_emails.audience) => class.
     *
     * @var array<string, class-string<\App\Libraries\GroupEmail\Audience>>
     */
    public array $audiences = [
        'event' => EventRegistrationAudience::class,
    ];

    /** Replies to every group email go here. */
    public string $replyTo = 'info@lcnl.org';

    /**
     * Queue priority (1 = first). 5 is "P5 – Bulk" in the email queue admin:
     * group emails go after booking confirmations, password resets and other
     * transactional mail (P1–P2), so a big send never holds those up.
     */
    public int $priority = 5;

    /** Test copies jump the queue so they arrive within a cron run or two. */
    public int $testPriority = 1;

    /** Used for {name} when a recipient has no name at all. */
    public string $fallbackName = 'Friend';

    /**
     * For the delivery estimate only: emails:send's ten-minute cap
     * (App\Commands\SendQueuedEmails, $TEN_MIN_CAP) and daily cap.
     */
    public int $sendsPerTenMinutes = 50;
    public int $sendsPerDay        = 950;

    public function __construct()
    {
        parent::__construct();

        $this->replyTo = (string) env('groupEmail.replyTo', $this->replyTo);
    }
}
