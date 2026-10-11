<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use DateTimeImmutable;
use DateTimeZone;

/**
 * LCNL Navratri Festival 2026 at the Dhamecha Lohana Centre.
 *
 * To put a night's live stream on the site, add its YouTube video ID to
 * $streams against that night's date. The ID is the part after "v=" or
 * "youtu.be/" in the link, e.g. https://youtu.be/abc123XYZ → 'abc123XYZ'.
 */
class Navratri extends BaseConfig
{
    public string $slug = 'navratri';

    public string $youtubeChannel = 'https://www.youtube.com/@lcnlmahajan';

    /** The home page banner and nav link are shown up to and including this date. */
    public string $promoteUntil = '2026-10-24';

    /**
     * YouTube video IDs keyed by night (Y-m-d). Empty until the link is known.
     *
     * @var array<string, string>
     */
    public array $streams = [
        '2026-10-11' => 'sTfmYBG_Ufo',
        '2026-10-12' => '',
        '2026-10-13' => '',
        '2026-10-14' => '',
        '2026-10-15' => '',
        '2026-10-16' => '',
        '2026-10-17' => '',
        '2026-10-18' => '',
        '2026-10-19' => '',
        '2026-10-24' => '',
    ];

    /**
     * Each evening of the festival. 'swatch' is the CSS background for the
     * colour chip and 'ink' the text colour that reads on it.
     *
     * @var list<array{date: string, label: string, colour: string, swatch: string, ink: string, soldOut?: bool, events?: list<string>}>
     */
    public array $nights = [
        ['date' => '2026-10-11', 'label' => 'Day 1', 'colour' => 'Orange', 'swatch' => '#f57c00', 'ink' => '#1a1a1a',
            'events' => ['YLS Kids Garba']],
        ['date' => '2026-10-12', 'label' => 'Day 2', 'colour' => 'Purple', 'swatch' => '#6a1b9a', 'ink' => '#fff'],
        ['date' => '2026-10-13', 'label' => 'Day 3', 'colour' => 'Peacock Green', 'swatch' => '#00796b', 'ink' => '#fff'],
        ['date' => '2026-10-14', 'label' => 'Day 4', 'colour' => 'Royal Blue', 'swatch' => '#1a3fb0', 'ink' => '#fff',
            'events' => ['Best Garba Competition']],
        ['date' => '2026-10-15', 'label' => 'Day 5', 'colour' => 'Yellow', 'swatch' => '#fbc02d', 'ink' => '#1a1a1a',
            'events' => ['Best Dandia Competition']],
        ['date' => '2026-10-16', 'label' => 'Day 6', 'colour' => 'Dark Green', 'swatch' => '#1b5e20', 'ink' => '#fff',
            'soldOut' => true],
        ['date' => '2026-10-17', 'label' => 'Day 7', 'colour' => 'Silver / Grey', 'swatch' => 'linear-gradient(135deg, #eceff1 0%, #b0bec5 50%, #eceff1 100%)', 'ink' => '#1a1a1a',
            'soldOut' => true, 'events' => ['Mahila Ladies Garba & Mela']],
        ['date' => '2026-10-18', 'label' => 'Day 8 · Aatham', 'colour' => 'Red', 'swatch' => '#c62828', 'ink' => '#fff',
            'events' => ['YLS Kids Garba']],
        ['date' => '2026-10-19', 'label' => 'Day 9', 'colour' => 'Multicoloured', 'swatch' => 'linear-gradient(90deg, #e53935, #fb8c00, #fdd835, #43a047, #1e88e5, #8e24aa)', 'ink' => '#fff',
            'events' => ['Best Dressed Competition']],
        ['date' => '2026-10-24', 'label' => 'Sharad Poonam', 'colour' => 'White', 'swatch' => '#ffffff', 'ink' => '#1a1a1a',
            'events' => ['Disco Dandia', "President's Prize"]],
    ];

    public function today(): string
    {
        // The app runs in UTC; garba runs past 11pm, when the UTC date has
        // already moved on during BST.
        return (new DateTimeImmutable('now', new DateTimeZone('Europe/London')))->format('Y-m-d');
    }

    public function isPromoted(): bool
    {
        return $this->today() <= $this->promoteUntil;
    }

    /** Tonight's night, or null when there is no garba today. */
    public function tonight(): ?array
    {
        foreach ($this->nights as $night) {
            if ($night['date'] === $this->today()) {
                return $night;
            }
        }

        return null;
    }

    public function streamFor(string $date): ?string
    {
        $id = trim($this->streams[$date] ?? '');

        return $id === '' ? null : $id;
    }

    /** £10 on Friday to Sunday, £5 Monday to Thursday. */
    public function ticketPrice(string $date): int
    {
        return (int) (new DateTimeImmutable($date))->format('N') >= 5 ? 10 : 5;
    }
}
