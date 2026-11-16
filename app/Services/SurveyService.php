<?php

namespace App\Services;

/**
 * Reads a questionnaire's answers from a form post and totals them for the
 * results page. It knows nothing about any one survey: it works from the
 * question list a config class supplies (see Config\TicketingSurvey).
 *
 * Form fields are named q[<id>] (q[<id>][] for tick boxes, q[<id>][<row>]
 * for a grid) and q[<id>_other] for an "Other" box.
 */
class SurveyService
{
    /** Longest comment kept, in characters. */
    public const MAX_COMMENT = 4000;

    /**
     * @param array<string, array> $questions keyed by id
     * @param array<string, mixed> $input     the posted q[] array
     *
     * @return array{answers: array<string, mixed>, errors: array<string, string>}
     */
    public function parse(array $questions, array $input): array
    {
        $answers = [];
        $errors  = [];

        foreach ($questions as $id => $q) {
            $raw = $input[$id] ?? null;

            switch ($q['type']) {
                case 'text':
                case 'textarea':
                    $max   = $q['type'] === 'text' ? (int) ($q['max'] ?? 255) : self::MAX_COMMENT;
                    $value = $this->clean(is_string($raw) ? $raw : '', $max, $q['type'] === 'textarea');

                    if ($value !== '' && ($q['input'] ?? '') === 'email' && ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$id] = 'Please check your email address, or leave it blank.';
                        break;
                    }
                    if ($value !== '') {
                        $answers[$id] = $value;
                    }
                    break;

                case 'single':
                case 'scale':
                    $value = is_scalar($raw) ? (string) $raw : '';
                    if (array_key_exists($value, $q['options'])) {
                        $answers[$id] = $value;
                    }
                    break;

                case 'multi':
                    $picked = array_values(array_unique(array_filter(
                        is_array($raw) ? array_map('strval', array_filter($raw, 'is_scalar')) : [],
                        static fn(string $v): bool => array_key_exists($v, $q['options'])
                    )));
                    // Keep the tick boxes in the order they are shown.
                    $picked = array_values(array_intersect(array_keys($q['options']), $picked));

                    if ($picked !== []) {
                        $answers[$id] = $picked;
                    }

                    if (! empty($q['other'])) {
                        $other = $this->clean(is_string($input[$id . '_other'] ?? null) ? $input[$id . '_other'] : '', 255);
                        if ($other !== '') {
                            $answers[$id . '_other'] = $other;
                        }
                    }
                    break;

                case 'grid':
                    $grid = [];
                    foreach (is_array($raw) ? $raw : [] as $row => $col) {
                        if (isset($q['rows'][$row]) && is_scalar($col) && array_key_exists((string) $col, $q['columns'] ?? [])) {
                            $grid[$row] = (string) $col;
                        }
                    }
                    if ($grid !== []) {
                        // Rows in display order.
                        $answers[$id] = array_intersect_key(array_replace($q['rows'], $grid), $grid);
                    }
                    break;
            }

            if (! empty($q['required']) && ! isset($errors[$id]) && ! $this->answered($answers, $id)) {
                $errors[$id] = $q['type'] === 'text'
                    ? 'Please enter ' . lcfirst($q['label']) . '.'
                    : 'Please answer “' . rtrim($q['label'], '?') . '”.';
            }
        }

        return ['answers' => $answers, 'errors' => $errors];
    }

    /**
     * Totals for every question, ready to display.
     *
     * Choice questions: counts per option (most picked first for tick boxes),
     * plus the "Other" answers. Grids: counts per row and column, and a score
     * (the mean of the column weights, ignoring columns weighted null) used to
     * rank rows. Free text: every answer with who gave it.
     *
     * @param array<string, array> $questions keyed by id
     * @param list<array>          $responses rows with decoded `answers`
     */
    public function summarise(array $questions, array $responses): array
    {
        $summary = [];

        foreach ($questions as $id => $q) {
            $answered = array_values(array_filter(
                $responses,
                fn(array $r): bool => $this->answered($r['answers'], $id)
            ));

            $item = ['question' => $q, 'answered' => count($answered)];

            switch ($q['type']) {
                case 'single':
                case 'scale':
                case 'multi':
                    $counts = array_fill_keys(array_keys($q['options']), 0);
                    $stale  = 0;
                    foreach ($answered as $r) {
                        foreach ((array) ($r['answers'][$id] ?? []) as $v) {
                            isset($counts[$v]) ? $counts[$v]++ : $stale++;
                        }
                    }
                    if ($q['type'] === 'multi') {
                        arsort($counts);
                    }
                    $item['counts'] = $counts;
                    $item['stale']  = $stale;
                    $item['other']  = $this->texts($answered, $id . '_other');
                    break;

                case 'grid':
                    $rows = [];
                    foreach ($q['rows'] as $rowId => $label) {
                        $counts = array_fill_keys(array_keys($q['columns']), 0);
                        foreach ($answered as $r) {
                            $col = $r['answers'][$id][$rowId] ?? null;
                            if ($col !== null && isset($counts[$col])) {
                                $counts[$col]++;
                            }
                        }

                        $weighted = 0;
                        $scored   = 0;
                        foreach ($counts as $col => $n) {
                            $weight = $q['columns'][$col]['score'] ?? null;
                            if ($weight !== null) {
                                $weighted += $weight * $n;
                                $scored   += $n;
                            }
                        }

                        $rows[$rowId] = [
                            'label'  => $label,
                            'counts' => $counts,
                            'total'  => array_sum($counts),
                            'score'  => $scored > 0 ? $weighted / $scored : null,
                        ];
                    }

                    // Highest priority first; unanswered rows last.
                    uasort($rows, static fn(array $a, array $b): int => [$b['score'] ?? -1, $b['counts'][array_key_first($b['counts'])]]
                        <=> [$a['score'] ?? -1, $a['counts'][array_key_first($a['counts'])]]);

                    $item['rows'] = $rows;
                    break;

                default:
                    $item['texts'] = $this->texts($answered, $id);
            }

            $summary[$id] = $item;
        }

        return $summary;
    }

    /**
     * Does $answers hold an answer to $id? An "Other" box counts for a tick
     * box question.
     */
    private function answered(array $answers, string $id): bool
    {
        return (isset($answers[$id]) && $answers[$id] !== '' && $answers[$id] !== [])
            || (isset($answers[$id . '_other']) && $answers[$id . '_other'] !== '');
    }

    /** @return list<array{text: string, name: string, anonymous: bool, id: int}> */
    private function texts(array $responses, string $key): array
    {
        $out = [];
        foreach ($responses as $r) {
            $text = $r['answers'][$key] ?? '';
            if (is_string($text) && $text !== '') {
                $out[] = [
                    'text'      => $text,
                    'name'      => (string) $r['respondent_name'],
                    // Anything but an explicit "named" is treated as anonymous.
                    'anonymous' => ($r['quote_preference'] ?? '') !== 'named',
                    'id'        => (int) $r['id'],
                ];
            }
        }

        return $out;
    }

    /** Trim, drop control characters, collapse runs of spaces, cap the length. */
    private function clean(string $value, int $max, bool $multiline = false): string
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = preg_replace($multiline ? '/[^\P{C}\n]/u' : '/\p{C}/u', ' ', $value) ?? '';
        $value = preg_replace('/[ \t]+/', ' ', $value) ?? '';
        if ($multiline) {
            $value = preg_replace("/\n{3,}/", "\n\n", $value) ?? '';
        }

        return mb_substr(trim($value), 0, $max);
    }
}
