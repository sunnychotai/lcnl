<?php
/**
 * 404 handler.
 *
 * This file is include()d by CodeIgniter's exception handler, not rendered
 * through the View service, so $this is the handler and layout inheritance is
 * not available here. Delegate to a normal view so the page can extend
 * layout/main and keep the site header, nav and footer.
 *
 * If the branded page fails to render for any reason (a broken layout, a
 * missing helper) fall back to plain HTML rather than surfacing a second
 * exception on top of the first.
 */
try {
    echo view('errors/404_page', [
        'title'           => 'Page not found',
        'metaDescription' => 'The page you were looking for could not be found on the '
            . 'Lohana Community North London website.',
        // Kept for local debugging only — never shown in production.
        'debugMessage'    => ENVIRONMENT !== 'production' ? ($message ?? '') : '',
    ]);
} catch (\Throwable $e) {
    log_message('critical', 'Branded 404 view failed to render: {msg}', ['msg' => $e->getMessage()]);
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">'
        . '<title>Page not found</title></head><body>'
        . '<h1>Page not found</h1>'
        . '<p>Sorry, we could not find that page. '
        . '<a href="' . esc(base_url(), 'attr') . '">Return to the homepage</a>.</p>'
        . '</body></html>';
}
