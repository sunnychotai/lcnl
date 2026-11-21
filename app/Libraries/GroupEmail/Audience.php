<?php

namespace App\Libraries\GroupEmail;

/**
 * A group of people a group email can go to, e.g. one event's registrants.
 *
 * Register implementations in Config\GroupEmail::$audiences. Recipients need
 * not be members: each one carries its own name and placeholder values.
 */
interface Audience
{
    /**
     * Build the audience from its stored reference (e.g. event id) and
     * filter. Returns null when the reference no longer exists. Unknown or
     * invalid filter values are dropped, missing ones take their defaults.
     */
    public static function make(?int $ref, array $filter = []): ?static;

    public function ref(): ?int;

    /** The cleaned filter, as stored on group_emails.filter_json. */
    public function filter(): array;

    /** e.g. "Registrants: Diwali Dinner (14 Nov 2026)". */
    public function label(): string;

    /** Where the admin came from, for back links. */
    public function backUrl(): string;

    /**
     * Filter controls for the compose page, as checkbox groups:
     * key => ['label' => ..., 'options' => [value => label]].
     *
     * @return array<string, array{label: string, options: array<string, string>}>
     */
    public function filterFields(): array;

    /**
     * Placeholders this audience fills, name => description. Written as
     * {name} or {{name}} in the subject and body.
     *
     * @return array<string, string>
     */
    public function placeholders(): array;

    /** One entry per email address: duplicates and bad addresses are skipped. */
    public function recipients(): RecipientList;

    /** Placeholder values to preview with when there are no recipients yet. */
    public function sampleVars(): array;
}
