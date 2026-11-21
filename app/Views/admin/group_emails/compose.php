<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var string $key
 * @var \App\Libraries\GroupEmail\Audience $audience
 * @var array|null $draft
 */
$subject  = (string) old('subject', $draft['subject'] ?? '');
$body     = (string) old('body_html', $draft['body_html'] ?? '');
$wrap     = (string) old('wrap_layout', $draft !== null ? (string) $draft['wrap_layout'] : '1') === '1';
$draftId  = (int) old('draft_id', $draft['id'] ?? 0);
$filter   = $audience->filter();
$statusBadges = [
    'queued'    => ['bg-primary', 'Queued'],
    'cancelled' => ['bg-secondary', 'Cancelled'],
];
?>

<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
    <div class="container position-relative text-center text-white py-3">
        <h1 class="fw-bold display-6 mb-2">Group email</h1>
        <p class="lead fs-5 mb-0"><?= esc($audience->label()) ?></p>
    </div>
</section>

<div class="container py-4">

    <?php if ($msg = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <a href="<?= esc($audience->backUrl(), 'attr') ?>" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i>Back to registrations
        </a>
        <a href="<?= site_url('admin/group-emails') ?>" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-envelope-paper me-1"></i>All group emails
        </a>
    </div>

    <?php if ($draft): ?>
        <div class="alert alert-info shadow-sm small">
            <i class="bi bi-pencil-square me-1"></i>
            Carrying on with a draft saved <?= date('j M Y, H:i', strtotime($draft['updated_at'])) ?>.
            <?php if ((int) $draft['test_count'] > 0): ?>
                Last test went to <?= esc($draft['last_test_to']) ?> at <?= date('H:i', strtotime($draft['last_test_at'])) ?>.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form id="groupEmailForm" method="post" action="<?= esc($baseUrl . '/queue', 'attr') ?>" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="draft_id" id="draftId" value="<?= $draftId ?: '' ?>">

        <div class="row g-4">
            <!-- Left: who, what, test, send -->
            <div class="col-lg-6">

                <!-- Recipients -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5 fw-bold mb-3"><i class="bi bi-people-fill me-2 text-brand"></i>Recipients</h2>

                        <?php foreach ($audience->filterFields() as $field => $def): ?>
                            <fieldset class="mb-3">
                                <legend class="form-label fs-6 mb-1"><?= esc($def['label']) ?></legend>
                                <!-- Always sent, so "nothing ticked" isn't read as "use the defaults" -->
                                <input type="hidden" name="filter[<?= esc($field, 'attr') ?>][]" value="">
                                <?php foreach ($def['options'] as $value => $label): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input js-preview" type="checkbox"
                                            id="f_<?= esc($field . '_' . $value, 'attr') ?>"
                                            name="filter[<?= esc($field, 'attr') ?>][]"
                                            value="<?= esc($value, 'attr') ?>"
                                            <?= in_array($value, $filter[$field] ?? [], true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="f_<?= esc($field . '_' . $value, 'attr') ?>"><?= esc($label) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </fieldset>
                        <?php endforeach; ?>

                        <p class="mb-1">
                            <strong id="recipientCount" class="fs-5 text-brand">…</strong>
                            <span id="recipientNoun">people</span> will get this email, one copy per email address.
                        </p>
                        <p class="small text-muted mb-2" id="skippedNote" hidden></p>

                        <button class="btn btn-sm btn-outline-secondary rounded-pill" type="button"
                            data-bs-toggle="collapse" data-bs-target="#recipientList" aria-expanded="false" aria-controls="recipientList">
                            <i class="bi bi-list-ul me-1"></i>Show recipients
                        </button>
                        <div class="collapse mt-3" id="recipientList">
                            <div class="table-responsive" style="max-height: 320px;">
                                <table class="table table-sm small align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr><th>Name</th><th>Email</th><th class="text-center">Party</th></tr>
                                    </thead>
                                    <tbody id="recipientRows"></tbody>
                                </table>
                            </div>
                            <div id="skippedList" class="mt-3" hidden>
                                <h3 class="h6 fw-bold">Left out</h3>
                                <ul class="small mb-0" id="skippedRows"></ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5 fw-bold mb-3"><i class="bi bi-envelope-fill me-2 text-brand"></i>Email</h2>

                        <div class="mb-3">
                            <label for="subject" class="form-label fw-semibold">Subject</label>
                            <input type="text" class="form-control js-preview" id="subject" name="subject"
                                maxlength="200" required value="<?= esc($subject, 'attr') ?>"
                                placeholder="e.g. {event_title}: your arrival details">
                        </div>

                        <div class="mb-3">
                            <div class="form-label fw-semibold mb-1">Placeholders</div>
                            <p class="small text-muted mb-2">
                                Click to insert where your cursor is. Each person's own details are filled in.
                                <code>{name}</code> and <code>{{name}}</code> both work.
                            </p>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach ($audience->placeholders() + ['current_year' => 'This year'] as $token => $desc): ?>
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill js-token"
                                        data-token="{<?= esc($token, 'attr') ?>}" title="<?= esc($desc, 'attr') ?>">
                                        {<?= esc($token) ?>}
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-2">
                            <label for="body_html" class="form-label fw-semibold mb-0">Message</label>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Editor mode">
                                <input type="radio" class="btn-check" name="editor_mode" id="modeEditor" value="editor" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="modeEditor"><i class="bi bi-type me-1"></i>Formatted</label>
                                <input type="radio" class="btn-check" name="editor_mode" id="modeSource" value="source" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="modeSource"><i class="bi bi-code-slash me-1"></i>HTML</label>
                            </div>
                        </div>
                        <p class="small text-muted mb-2">
                            Paste formatted text (from Word, Outlook or a web page) into <em>Formatted</em>,
                            or paste raw HTML into <em>HTML</em>.
                        </p>
                        <textarea id="body_html" name="body_html" class="form-control font-monospace small js-preview"
                            rows="16"><?= esc($body) ?></textarea>

                        <input type="hidden" name="wrap_layout" value="0">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input js-preview" type="checkbox" role="switch" id="wrapLayout"
                                name="wrap_layout" value="1" <?= $wrap ? 'checked' : '' ?>>
                            <label class="form-check-label" for="wrapLayout">Wrap in the LCNL header and footer</label>
                        </div>
                        <p class="small text-muted mt-2 mb-0">
                            <i class="bi bi-reply me-1"></i>Replies go to <strong><?= esc($replyTo) ?></strong>.
                        </p>
                    </div>
                </div>

                <!-- Test -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5 fw-bold mb-2"><i class="bi bi-send-check me-2 text-brand"></i>Send a test</h2>
                        <p class="small text-muted">
                            One copy, filled in with the first recipient's details and marked [TEST].
                            It jumps the queue, so it usually arrives within a few minutes.
                        </p>
                        <div class="input-group">
                            <input type="email" class="form-control" id="testEmail" value="<?= esc($testEmail, 'attr') ?>"
                                placeholder="you@example.com" aria-label="Send the test to">
                            <button class="btn btn-outline-primary" type="button" id="sendTest">
                                <i class="bi bi-send me-1"></i>Send test
                            </button>
                        </div>
                        <div id="testResult" class="small mt-2" role="status" aria-live="polite"></div>
                    </div>
                </div>

                <!-- Queue -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5 fw-bold mb-2"><i class="bi bi-envelope-arrow-up me-2 text-brand"></i>Send to everyone</h2>
                        <div id="warnings"></div>
                        <p class="small text-muted" id="estimate"></p>
                        <button type="submit" class="btn btn-success rounded-pill px-4" id="queueButton" disabled>
                            <i class="bi bi-envelope-check me-2"></i><span>Queue emails</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: live preview -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm position-sticky" style="top: 1rem;">
                    <div class="card-body">
                        <h2 class="h5 fw-bold mb-1"><i class="bi bi-eye me-2 text-brand"></i>Preview</h2>
                        <p class="small text-muted mb-2" id="previewWho">Loading…</p>
                        <div class="border rounded-top px-3 py-2 bg-light small">
                            <span class="text-muted">Subject:</span> <strong id="previewSubject"></strong>
                        </div>
                        <iframe id="previewFrame" title="Email preview" sandbox=""
                            class="w-100 border border-top-0 rounded-bottom bg-white" style="height: 720px;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php if ($previous): ?>
        <div class="card border-0 shadow-sm mt-2">
            <div class="card-body">
                <h2 class="h5 fw-bold mb-3">Sent before to this event</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Queued</th><th>Subject</th><th>Status</th><th class="text-end">Sent / recipients</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($previous as $p): [$cls, $label] = $statusBadges[$p['status']] ?? ['bg-light text-dark', $p['status']]; ?>
                                <tr>
                                    <td class="small"><?= $p['queued_at'] ? date('j M Y, H:i', strtotime($p['queued_at'])) : '' ?></td>
                                    <td><a href="<?= site_url('admin/group-emails/' . $p['id']) ?>"><?= esc($p['subject']) ?></a></td>
                                    <td><span class="badge <?= $cls ?>"><?= esc($label) ?></span></td>
                                    <td class="text-end"><?= $progress[$p['id']]['sent'] ?? 0 ?> / <?= (int) $p['recipient_count'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.9.1/tinymce.min.js" referrerpolicy="origin"></script>
<script>
(function () {
    const form      = document.getElementById('groupEmailForm');
    const textarea  = document.getElementById('body_html');
    const subject   = document.getElementById('subject');
    const draftId   = document.getElementById('draftId');
    const queueBtn  = document.getElementById('queueButton');
    const baseUrl   = <?= json_encode($baseUrl, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>;
    const siteBase  = <?= json_encode(rtrim(base_url(), '/') . '/', JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>;

    let mode = /<html|<style/i.test(textarea.value) ? 'source' : 'editor';
    let lastFocus = 'body';
    let recipientCount = 0;
    let testsSent = <?= (int) ($draft['test_count'] ?? 0) ?>;

    /* ---------- Editor ---------- */

    function editor() {
        return window.tinymce ? tinymce.get('body_html') : null;
    }

    function startEditor() {
        if (!window.tinymce) { // CDN unreachable: the plain textarea still works
            mode = 'source';
            document.getElementById('modeSource').checked = true;
            return;
        }
        tinymce.init({
            target: textarea,
            license_key: 'gpl',
            promotion: false,
            branding: false,
            menubar: false,
            plugins: 'autolink link lists table code image autoresize',
            toolbar: 'undo redo | blocks | bold italic underline forecolor backcolor | alignleft aligncenter alignright | bullist numlist | link image table | removeformat code',
            toolbar_mode: 'wrap',
            min_height: 380,
            max_height: 800,
            // Email needs full https:// links and image addresses.
            relative_urls: false,
            remove_script_host: false,
            document_base_url: siteBase,
            entity_encoding: 'raw',
            // Pasted-in images become data: URLs, which email apps block.
            paste_data_images: false,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #333; }',
            setup: function (ed) {
                ed.on('input change undo redo SetContent', schedulePreview);
                ed.on('focus', function () { lastFocus = 'body'; });
            },
        });
    }

    function syncBody() {
        const ed = editor();
        if (ed) { ed.save(); }
    }

    document.querySelectorAll('input[name="editor_mode"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (this.value === mode) { return; }
            if (this.value === 'editor') {
                if (/<html|<style/i.test(textarea.value) &&
                    !confirm('This looks like a complete HTML email. The formatted editor may remove parts of it, such as <style> blocks. Switch anyway?')) {
                    document.getElementById('modeSource').checked = true;
                    return;
                }
                mode = 'editor';
                startEditor();
            } else {
                syncBody();
                const ed = editor();
                if (ed) { ed.remove(); }
                mode = 'source';
            }
        });
    });

    document.getElementById(mode === 'editor' ? 'modeEditor' : 'modeSource').checked = true;
    if (mode === 'editor') { startEditor(); }

    /* ---------- Placeholders ---------- */

    subject.addEventListener('focus', function () { lastFocus = 'subject'; });
    textarea.addEventListener('focus', function () { lastFocus = 'body'; });

    function insertAtCaret(field, text) {
        const start = field.selectionStart ?? field.value.length;
        const end   = field.selectionEnd ?? field.value.length;
        field.value = field.value.slice(0, start) + text + field.value.slice(end);
        field.selectionStart = field.selectionEnd = start + text.length;
        field.focus();
    }

    document.querySelectorAll('.js-token').forEach(function (btn) {
        btn.addEventListener('mousedown', function (e) { e.preventDefault(); }); // keep the caret
        btn.addEventListener('click', function () {
            const token = this.dataset.token;
            const ed = editor();
            if (lastFocus === 'subject') {
                insertAtCaret(subject, token);
            } else if (ed) {
                ed.focus();
                ed.insertContent(token);
            } else {
                insertAtCaret(textarea, token);
            }
            schedulePreview();
        });
    });

    /* ---------- Preview ---------- */

    let previewTimer = null;
    let previewSeq = 0;

    function schedulePreview() {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(refreshPreview, 500);
    }

    function el(tag, text, cls) {
        const node = document.createElement(tag);
        if (text !== undefined) { node.textContent = text; }
        if (cls) { node.className = cls; }
        return node;
    }

    function refreshPreview() {
        syncBody();
        const seq = ++previewSeq;

        fetch(baseUrl + '/preview', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (seq !== previewSeq) { return; } // a newer preview is on its way
                render(data);
            })
            .catch(function () {
                document.getElementById('previewWho').textContent = 'The preview could not be loaded. Check you are still logged in.';
            });
    }

    function render(data) {
        document.getElementById('previewFrame').srcdoc = data.html || '';
        document.getElementById('previewSubject').textContent = data.subject || '(no subject)';
        document.getElementById('previewWho').textContent = data.sample
            ? 'As ' + data.sample + ' will see it.'
            : 'With sample details: no one matches yet.';

        recipientCount = data.recipients.length;
        document.getElementById('recipientCount').textContent = recipientCount;
        document.getElementById('recipientNoun').textContent = recipientCount === 1 ? 'person' : 'people';

        const rows = document.getElementById('recipientRows');
        rows.replaceChildren();
        data.recipients.forEach(function (r) {
            const tr = el('tr');
            tr.append(el('td', r.name), el('td', r.email), el('td', r.party ?? '', 'text-center'));
            rows.append(tr);
        });
        if (!recipientCount) {
            const tr = el('tr');
            const td = el('td', 'No one matches.', 'text-muted');
            td.colSpan = 3;
            tr.append(td);
            rows.append(tr);
        }

        const skipped = data.skipped || [];
        const note = document.getElementById('skippedNote');
        note.hidden = !skipped.length;
        note.textContent = skipped.length + ' registration' + (skipped.length === 1 ? ' is' : 's are') +
            ' left out (repeat registrations or invalid addresses). See the list for details.';
        document.getElementById('skippedList').hidden = !skipped.length;
        const skippedRows = document.getElementById('skippedRows');
        skippedRows.replaceChildren();
        skipped.forEach(function (s) {
            skippedRows.append(el('li', (s.name ? s.name + ' – ' : '') + s.email + ': ' + s.reason));
        });

        const warnings = document.getElementById('warnings');
        warnings.replaceChildren();
        (data.warnings || []).forEach(function (w) {
            const div = el('div', '', 'alert alert-warning small py-2 mb-2');
            div.append(el('i', '', 'bi bi-exclamation-triangle-fill me-1'), document.createTextNode(w));
            warnings.append(div);
        });

        const est = data.estimate || {};
        let estimate = '';
        if (recipientCount) {
            estimate = 'The queue sends about 50 emails every 10 minutes, so these should all have gone in about ' +
                humanMinutes(est.minutes) + (est.ahead ? ' (' + est.ahead + ' other emails are waiting ahead of them)' : '') + '.';
            if (est.over_day) {
                estimate += ' That is more than the daily limit, so some will go tomorrow.';
            }
        }
        document.getElementById('estimate').textContent = estimate;

        queueBtn.disabled = recipientCount === 0;
        queueBtn.querySelector('span').textContent = recipientCount
            ? 'Queue for ' + recipientCount + ' ' + (recipientCount === 1 ? 'person' : 'people')
            : 'No one to send to';
    }

    function humanMinutes(m) {
        if (m < 60) { return m + ' minutes'; }
        const h = Math.floor(m / 60), r = m % 60;
        return h + ' hour' + (h === 1 ? '' : 's') + (r ? ' ' + r + ' minutes' : '');
    }

    form.querySelectorAll('.js-preview').forEach(function (input) {
        input.addEventListener(input.type === 'checkbox' ? 'change' : 'input', schedulePreview);
    });

    /* ---------- Test ---------- */

    document.getElementById('sendTest').addEventListener('click', function () {
        const btn = this;
        const result = document.getElementById('testResult');
        const to = document.getElementById('testEmail').value.trim();

        syncBody();
        const fd = new FormData(form);
        fd.append('test_email', to);

        btn.disabled = true;
        result.className = 'small mt-2 text-muted';
        result.textContent = 'Queuing test…';

        fetch(baseUrl + '/test', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.draft_id) { draftId.value = data.draft_id; }
                if (data.ok) { testsSent++; }
                result.className = 'small mt-2 ' + (data.ok ? 'text-success' : 'text-danger');
                result.textContent = data.message;
            })
            .catch(function () {
                result.className = 'small mt-2 text-danger';
                result.textContent = 'The test could not be queued. Check you are still logged in.';
            })
            .finally(function () { btn.disabled = false; });
    });

    /* ---------- Queue ---------- */

    form.addEventListener('submit', function (e) {
        syncBody();
        let msg = 'Queue this email for ' + recipientCount + ' ' + (recipientCount === 1 ? 'person' : 'people') + '?\n\n' +
            'Copies that have not gone yet can be cancelled afterwards.';
        if (!testsSent) {
            msg = 'You have not sent a test yet.\n\n' + msg;
        }
        if (!recipientCount || !confirm(msg)) {
            e.preventDefault();
            return;
        }
        queueBtn.disabled = true;
    });

    refreshPreview();
})();
</script>

<?= $this->endSection() ?>
