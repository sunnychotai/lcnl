<?php

namespace App\Libraries\GroupEmail;

/**
 * Who a group email goes to, and who was left out and why.
 */
final class RecipientList
{
    /**
     * @param list<array{email: string, name: string, vars: array<string, string>}> $recipients
     *        `name` is the full name for the To: header; `vars` fills the placeholders.
     * @param list<array{email: string, name: string, reason: string}> $skipped
     */
    public function __construct(
        public readonly array $recipients,
        public readonly array $skipped = [],
    ) {
    }

    public function count(): int
    {
        return count($this->recipients);
    }

    public function first(): ?array
    {
        return $this->recipients[0] ?? null;
    }
}
