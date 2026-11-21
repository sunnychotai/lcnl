<?php

namespace App\Libraries\GroupEmail;

use App\Models\EventModel;
use App\Models\EventRegistrationModel;

/**
 * Everyone registered for one event. Registrants needn't be members.
 *
 * Someone who registered more than once gets one email, using their latest
 * registration (its name and party size).
 */
final class EventRegistrationAudience implements Audience
{
    private const STATUSES = [
        'submitted' => 'Submitted',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
    ];

    private const DEFAULT_STATUSES = ['submitted', 'confirmed'];

    private function __construct(
        private readonly array $event,
        private readonly array $statuses,
    ) {
    }

    public static function make(?int $ref, array $filter = []): ?static
    {
        $event = $ref ? (new EventModel())->find($ref) : null;

        if (!$event) {
            return null;
        }

        // A missing key means "use the defaults"; an empty list means the
        // admin unticked everything, which is honoured (no recipients).
        $statuses = array_key_exists('statuses', $filter)
            ? array_values(array_intersect(array_keys(self::STATUSES), (array) $filter['statuses']))
            : self::DEFAULT_STATUSES;

        return new self($event, $statuses);
    }

    public function ref(): ?int
    {
        return (int) $this->event['id'];
    }

    public function filter(): array
    {
        return ['statuses' => $this->statuses];
    }

    public function label(): string
    {
        $date = !empty($this->event['event_date'])
            ? ' (' . date('j M Y', strtotime($this->event['event_date'])) . ')'
            : '';

        return 'Registrants: ' . $this->event['title'] . $date;
    }

    public function backUrl(): string
    {
        return site_url('admin/content/events/event-registrations/' . $this->event['id']);
    }

    public function filterFields(): array
    {
        return [
            'statuses' => ['label' => 'Registrations to include', 'options' => self::STATUSES],
        ];
    }

    public function placeholders(): array
    {
        return [
            'name'           => 'First name (full name if there is no first name)',
            'first_name'     => 'First name',
            'full_name'      => 'First and last name',
            'party'          => 'Party size (the registrant plus guests)',
            'guests'         => 'Number of guests',
            'event_title'    => 'Event title',
            'event_date'     => 'Event date, e.g. Saturday 14 November 2026',
            'event_time'     => 'Event start and end time',
            'event_location' => 'Event location',
        ];
    }

    public function recipients(): RecipientList
    {
        if ($this->statuses === []) {
            return new RecipientList([]);
        }

        $rows = (new EventRegistrationModel())
            ->where('event_id', $this->event['id'])
            ->whereIn('status', $this->statuses)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $recipients = [];
        $skipped    = [];
        $seen       = [];

        // Newest first, so the first row seen for an address is its latest.
        foreach ($rows as $row) {
            $email = strtolower(trim((string) $row['email']));
            $full  = trim(trim((string) $row['first_name']) . ' ' . trim((string) $row['last_name']));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped[] = ['email' => (string) $row['email'], 'name' => $full, 'reason' => 'Invalid email address'];
                continue;
            }

            if (isset($seen[$email])) {
                $skipped[] = ['email' => $email, 'name' => $full, 'reason' => 'Registered again later; the latest registration is used'];
                continue;
            }

            $seen[$email] = true;
            $recipients[] = [
                'email' => $email,
                'name'  => $full,
                'vars'  => $this->vars($row),
            ];
        }

        // Oldest registration first reads more naturally in the admin.
        return new RecipientList(array_reverse($recipients), $skipped);
    }

    public function sampleVars(): array
    {
        return $this->vars([
            'first_name'       => 'Asha',
            'last_name'        => 'Patel',
            'num_participants' => 1,
            'num_guests'       => 2,
        ]);
    }

    private function vars(array $row): array
    {
        $first = trim((string) ($row['first_name'] ?? ''));
        $full  = trim($first . ' ' . trim((string) ($row['last_name'] ?? '')));
        $guests = (int) ($row['num_guests'] ?? 0);

        return [
            'name'           => $first !== '' ? $first : ($full !== '' ? $full : config('GroupEmail')->fallbackName),
            'first_name'     => $first,
            'full_name'      => $full,
            'party'          => (string) ((int) ($row['num_participants'] ?? 1) + $guests),
            'guests'         => (string) $guests,
            'event_title'    => (string) $this->event['title'],
            'event_date'     => !empty($this->event['event_date']) ? date('l j F Y', strtotime($this->event['event_date'])) : '',
            'event_time'     => $this->eventTime(),
            'event_location' => (string) ($this->event['location'] ?? ''),
        ];
    }

    private function eventTime(): string
    {
        $fmt = static fn ($t) => $t ? date('g:ia', strtotime($t)) : '';
        $from = $fmt($this->event['time_from'] ?? null);
        $to   = $fmt($this->event['time_to'] ?? null);

        return $from !== '' && $to !== '' ? $from . ' – ' . $to : $from;
    }
}
