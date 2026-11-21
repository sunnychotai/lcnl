<?php

namespace App\Libraries\GroupEmail;

/**
 * Fills {name} / {{name}} placeholders. Both spellings work, so the double
 * braces used by the older templates ({{current_year}}) carry on working.
 *
 * Only known names are replaced; anything else is left as written, so a
 * typo shows up in the preview rather than vanishing.
 */
final class PlaceholderRenderer
{
    private const PATTERN = '/\{\{\s*([a-z][a-z0-9_]*)\s*\}\}|\{\s*([a-z][a-z0-9_]*)\s*\}/i';

    /**
     * @param array<string, scalar|null> $vars
     * @param bool $html Escape the values for HTML (the body) or not (the subject)
     */
    public static function render(string $template, array $vars, bool $html): string
    {
        $vars = array_change_key_case($vars, CASE_LOWER);

        return preg_replace_callback(self::PATTERN, static function (array $m) use ($vars, $html) {
            $key = strtolower($m[1] !== '' ? $m[1] : $m[2]);

            if (!array_key_exists($key, $vars)) {
                return $m[0];
            }

            $value = (string) $vars[$key];

            return $html
                ? htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8')
                : str_replace(["\r", "\n"], ' ', $value);
        }, $template);
    }

    /**
     * Placeholder-looking tokens that aren't in $known, as written.
     *
     * @param list<string> $known
     * @return list<string>
     */
    public static function unknown(string $template, array $known): array
    {
        $known = array_map('strtolower', $known);

        preg_match_all(self::PATTERN, $template, $matches, PREG_SET_ORDER);

        $unknown = [];
        foreach ($matches as $m) {
            $key = strtolower($m[1] !== '' ? $m[1] : $m[2]);
            if (!in_array($key, $known, true)) {
                $unknown[$m[0]] = true;
            }
        }

        return array_keys($unknown);
    }
}
