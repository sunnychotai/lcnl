<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\TicketingSurvey $config
 * @var array $sections
 * @var array $gridColumns
 * @var bool  $isOpen
 */
$errors  = session('errors') ?? [];
$req     = '<span class="mela-req" aria-hidden="true">*</span>';
$hasOld  = old('q') !== null;

// Raw old input (escaped where it is printed).
$old = static function (string $key, $default = null) {
  $v = old('q.' . $key, $default, false);

  return $v ?? $default;
};

$fieldError = static function (string $id) use ($errors): string {
  return isset($errors[$id])
    ? '<div class="invalid-feedback d-block" id="err-' . esc($id, 'attr') . '">' . esc($errors[$id]) . '</div>'
    : '';
};

$describedBy = static function (string $id, array $q) use ($errors): string {
  $ids = [];
  if (! empty($q['help'])) {
    $ids[] = 'help-' . $id;
  }
  if (isset($errors[$id])) {
    $ids[] = 'err-' . $id;
  }

  return $ids ? ' aria-describedby="' . esc(implode(' ', $ids), 'attr') . '"' : '';
};

$help = static fn(string $id, array $q): string => ! empty($q['help'])
  ? '<div class="form-text mt-0 mb-2" id="help-' . esc($id, 'attr') . '">' . esc($q['help']) . '</div>'
  : '';

$questionCount = 0;
foreach ($sections as $s) {
  $questionCount += count($s['questions']);
}
?>

<?= view('partials/page_header', [
  'overlay'    => 'peacock',
  'eyebrow'    => 'For LCNL committee members',
  'heading'    => 'Ticketing and events questionnaire',
  'subheading' => 'Help shape how LCNL sells tickets and handles event money',
]) ?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">

      <?php if (! $isOpen): ?>

        <div class="mela-form-card">
          <div class="card-body p-4 p-md-5 text-center mela-closed">
            <i class="bi bi-clipboard-check"></i>
            <h2 class="h4 fw-bold mt-3">This questionnaire has closed</h2>
            <p class="text-muted mb-0">Thank you to everyone who took part. If you still have something to add,
              please email <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>"><?= esc($config->contactEmail) ?></a>.</p>
          </div>
        </div>

      <?php else: ?>

        <div class="svy-intro mb-4">
          <p class="lead mb-3">
            We are building online ticketing into the LCNL website. Before we design it, we want to hear from the
            people who actually run our events: what works today, what is painful, and what the new system must get right.
          </p>
          <ul class="svy-facts">
            <li><i class="bi bi-clock"></i> About 10 minutes</li>
            <li><i class="bi bi-check2-square"></i> Mostly tick boxes; skip anything you can’t answer</li>
            <li><i class="bi bi-chat-quote"></i> Be as honest as you like: criticism of the current process,
              or of the whole idea, is exactly what we need</li>
          </ul>
          <p class="small text-muted mb-0">
            Your answers go to the small team planning the system. You choose at the end whether we may use your name
            when we share a summary.
          </p>
        </div>

        <?php if (! empty($errors)): ?>
          <div class="alert alert-danger" role="alert" tabindex="-1" id="formErrors">
            <h2 class="h6 fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-1"></i>Please check the following</h2>
            <ul class="mb-0">
              <?php foreach ($errors as $id => $e): ?>
                <li>
                  <?php if ($id !== 'form'): ?>
                    <a href="#field-<?= esc($id, 'attr') ?>" class="alert-link"><?= esc($e) ?></a>
                  <?php else: ?>
                    <?= esc($e) ?>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="svy-progress" aria-hidden="true">
          <div class="svy-progress-bar"><span id="svyProgress" style="width:0"></span></div>
          <span class="svy-progress-label" id="svyProgressLabel">0 of <?= $questionCount ?> answered</span>
        </div>

        <form method="post" action="<?= esc(site_url($config->slug)) ?>" novalidate id="surveyForm"
          class="mela-form-card" data-draft-key="lcnl-survey-<?= esc($config->key, 'attr') ?>"
          data-has-old="<?= $hasOld ? '1' : '0' ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="form_time" value="<?= time() ?>">

          <!-- Honeypot: hidden from people, so anything here is a bot. -->
          <div class="d-none" aria-hidden="true">
            <label for="website">Leave this field blank</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
          </div>

          <div class="mela-form-head">
            <h2 class="h4"><i class="bi bi-clipboard-data me-2"></i>Your feedback</h2>
            <p>Questions marked <span class="mela-req">*</span> are required. Everything else is optional.</p>
          </div>

          <?php foreach ($sections as $n => $section): ?>
            <section class="mela-section" aria-labelledby="sec-<?= esc($section['id'], 'attr') ?>">
              <div class="mela-section-head">
                <span class="mela-step-num"><?= $n + 1 ?></span>
                <h3 id="sec-<?= esc($section['id'], 'attr') ?>"><?= esc($section['title']) ?></h3>
              </div>
              <?php if (! empty($section['intro'])): ?>
                <p class="text-muted small mt-n2 mb-3"><?= esc($section['intro']) ?></p>
              <?php endif; ?>

              <?php foreach ($section['questions'] as $id => $q): ?>
                <?php
                $name    = 'q[' . $id . ']';
                $isReq   = ! empty($q['required']);
                $invalid = isset($errors[$id]) ? ' is-invalid' : '';
                $attrId  = 'q-' . $id;
                ?>
                <div class="svy-q<?= $invalid ? ' has-error' : '' ?>" id="field-<?= esc($id, 'attr') ?>" data-question="<?= esc($id, 'attr') ?>">

                  <?php if ($q['type'] === 'text' || $q['type'] === 'textarea'): ?>
                    <label for="<?= esc($attrId, 'attr') ?>" class="form-label svy-label"><?= esc($q['label']) ?> <?= $isReq ? $req : '' ?></label>
                    <?= $help($id, $q) ?>
                    <?php if ($q['type'] === 'text'): ?>
                      <input type="<?= esc($q['input'] ?? 'text', 'attr') ?>" class="form-control<?= $invalid ?>"
                        id="<?= esc($attrId, 'attr') ?>" name="<?= esc($name, 'attr') ?>"
                        value="<?= esc((string) $old($id, '')) ?>" maxlength="<?= (int) ($q['max'] ?? 255) ?>"
                        <?= ! empty($q['autocomplete']) ? 'autocomplete="' . esc($q['autocomplete'], 'attr') . '"' : '' ?>
                        <?= $isReq ? 'required aria-required="true"' : '' ?><?= $describedBy($id, $q) ?>>
                    <?php else: ?>
                      <textarea class="form-control<?= $invalid ?>" id="<?= esc($attrId, 'attr') ?>" name="<?= esc($name, 'attr') ?>"
                        rows="<?= (int) ($q['rows'] ?? 4) ?>" maxlength="<?= \App\Services\SurveyService::MAX_COMMENT ?>"
                        <?= $describedBy($id, $q) ?>><?= esc((string) $old($id, '')) ?></textarea>
                    <?php endif; ?>

                  <?php elseif ($q['type'] === 'single' || $q['type'] === 'multi'): ?>
                    <?php
                    $multi   = $q['type'] === 'multi';
                    $current = $multi ? (array) $old($id, []) : (string) $old($id, '');
                    ?>
                    <fieldset<?= $describedBy($id, $q) ?>>
                      <legend class="form-label svy-label"><?= esc($q['label']) ?> <?= $isReq ? $req : '' ?></legend>
                      <?= $help($id, $q) ?>
                      <div class="svy-options<?= count($q['options']) > 5 ? ' is-two-col' : '' ?>">
                        <?php foreach ($q['options'] as $value => $label): ?>
                          <?php
                          $optId   = $attrId . '-' . $value;
                          $checked = $multi ? in_array((string) $value, $current, true) : $current === (string) $value;
                          ?>
                          <div class="form-check svy-option">
                            <input class="form-check-input<?= $invalid ?>" type="<?= $multi ? 'checkbox' : 'radio' ?>"
                              id="<?= esc($optId, 'attr') ?>" name="<?= esc($name . ($multi ? '[]' : ''), 'attr') ?>"
                              value="<?= esc($value, 'attr') ?>"<?= $checked ? ' checked' : '' ?>
                              <?= (! $multi && $isReq) ? 'required' : '' ?>>
                            <label class="form-check-label" for="<?= esc($optId, 'attr') ?>"><?= esc($label) ?></label>
                          </div>
                        <?php endforeach; ?>
                      </div>
                      <?php if (! empty($q['other'])): ?>
                        <label for="<?= esc($attrId, 'attr') ?>-other" class="visually-hidden">Other: <?= esc($q['label']) ?></label>
                        <input type="text" class="form-control form-control-sm svy-other" id="<?= esc($attrId, 'attr') ?>-other"
                          name="<?= esc('q[' . $id . '_other]', 'attr') ?>" maxlength="255"
                          placeholder="Other (please say)" value="<?= esc((string) $old($id . '_other', '')) ?>">
                      <?php endif; ?>
                    </fieldset>

                  <?php elseif ($q['type'] === 'scale'): ?>
                    <?php $current = (string) $old($id, ''); ?>
                    <fieldset<?= $describedBy($id, $q) ?>>
                      <legend class="form-label svy-label"><?= esc($q['label']) ?> <?= $isReq ? $req : '' ?></legend>
                      <?= $help($id, $q) ?>
                      <div class="svy-scale" role="radiogroup">
                        <?php foreach ($q['options'] as $value => $label): ?>
                          <?php $optId = $attrId . '-' . $value; ?>
                          <input type="radio" class="btn-check" id="<?= esc($optId, 'attr') ?>" name="<?= esc($name, 'attr') ?>"
                            value="<?= esc($value, 'attr') ?>" autocomplete="off"<?= $current === (string) $value ? ' checked' : '' ?>>
                          <label class="btn svy-scale-btn<?= in_array($value, ['na', 'unsure'], true) ? ' is-aside' : '' ?>"
                            for="<?= esc($optId, 'attr') ?>"><?= esc($label) ?></label>
                        <?php endforeach; ?>
                      </div>
                    </fieldset>

                  <?php elseif ($q['type'] === 'grid'): ?>
                    <?php $current = (array) $old($id, []); ?>
                    <fieldset<?= $describedBy($id, $q) ?>>
                      <legend class="form-label svy-label"><?= esc($q['label']) ?> <?= $isReq ? $req : '' ?></legend>
                      <?= $help($id, $q) ?>
                      <div class="svy-grid">
                        <div class="svy-grid-head" aria-hidden="true">
                          <span></span>
                          <?php foreach ($gridColumns as $col): ?>
                            <span><?= esc($col['label']) ?></span>
                          <?php endforeach; ?>
                        </div>
                        <?php foreach ($q['rows'] as $rowId => $rowLabel): ?>
                          <?php $rowAttr = $attrId . '-' . $rowId; ?>
                          <div class="svy-grid-row" role="radiogroup" aria-labelledby="<?= esc($rowAttr, 'attr') ?>">
                            <span class="svy-grid-label" id="<?= esc($rowAttr, 'attr') ?>"><?= esc($rowLabel) ?></span>
                            <?php foreach ($gridColumns as $colId => $col): ?>
                              <?php $optId = $rowAttr . '-' . $colId; ?>
                              <span class="svy-grid-cell">
                                <input type="radio" class="btn-check" id="<?= esc($optId, 'attr') ?>"
                                  name="<?= esc($name . '[' . $rowId . ']', 'attr') ?>" value="<?= esc($colId, 'attr') ?>"
                                  autocomplete="off"<?= ($current[$rowId] ?? null) === $colId ? ' checked' : '' ?>>
                                <label class="btn svy-grid-btn is-<?= esc($colId, 'attr') ?>" for="<?= esc($optId, 'attr') ?>"><?= esc($col['label']) ?></label>
                              </span>
                            <?php endforeach; ?>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </fieldset>
                  <?php endif; ?>

                  <?= $fieldError($id) ?>
                </div>
              <?php endforeach; ?>
            </section>
          <?php endforeach; ?>

          <div class="mela-section svy-submit">
            <button type="submit" class="btn btn-brand btn-lg px-5" id="surveySubmit">
              <i class="bi bi-send me-2"></i>Send my answers
            </button>
            <p class="small text-muted mt-3 mb-0" id="draftNote" hidden>
              <i class="bi bi-cloud-check me-1"></i>Your answers are saved in this browser as you go, so you can come back to finish.
            </p>
          </div>
        </form>

      <?php endif; ?>
    </div>
  </div>
</div>

<?php if ($isOpen): ?>
<script>
  (function () {
    const form = document.getElementById('surveyForm');
    if (!form) return;

    const errors = document.getElementById('formErrors');
    if (errors) errors.focus();

    // ---- Progress: a question counts once anything in it is filled in.
    const questions = Array.from(form.querySelectorAll('[data-question]'));
    const bar = document.getElementById('svyProgress');
    const label = document.getElementById('svyProgressLabel');

    const isAnswered = (q) => Array.from(q.querySelectorAll('input, textarea')).some((el) =>
      (el.type === 'radio' || el.type === 'checkbox') ? el.checked : el.value.trim() !== '');

    function updateProgress() {
      const done = questions.filter(isAnswered).length;
      bar.style.width = Math.round(done / questions.length * 100) + '%';
      label.textContent = done + ' of ' + questions.length + ' answered';
    }

    // ---- Draft: kept in this browser only, so a closed tab doesn't lose answers.
    const key = form.dataset.draftKey;
    const skip = ['csrf_test_name', 'form_time', 'website'];
    const store = {
      get() { try { return JSON.parse(localStorage.getItem(key) || 'null'); } catch (e) { return null; } },
      set(v) { try { localStorage.setItem(key, JSON.stringify(v)); return true; } catch (e) { return false; } },
      clear() { try { localStorage.removeItem(key); } catch (e) { /* ignore */ } },
    };

    function saveDraft() {
      const data = {};
      new FormData(form).forEach((value, name) => {
        if (skip.includes(name)) return;
        (data[name] = data[name] || []).push(value);
      });
      if (store.set(data)) document.getElementById('draftNote').hidden = false;
    }

    function restoreDraft() {
      // The server's own copy wins after a failed submit.
      if (form.dataset.hasOld === '1') return;
      const data = store.get();
      if (!data) return;
      form.querySelectorAll('input[name^="q["], textarea[name^="q["]').forEach((el) => {
        const values = data[el.name];
        if (!values) return;
        if (el.type === 'radio' || el.type === 'checkbox') {
          el.checked = values.includes(el.value);
        } else {
          el.value = values[0];
        }
      });
      document.getElementById('draftNote').hidden = false;
    }

    restoreDraft();
    updateProgress();

    let timer;
    form.addEventListener('input', () => {
      updateProgress();
      clearTimeout(timer);
      timer = setTimeout(saveDraft, 400);
    });
    form.addEventListener('change', () => { updateProgress(); saveDraft(); });

    const btn = document.getElementById('surveySubmit');
    const btnHtml = btn.innerHTML;
    form.addEventListener('submit', () => {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Sending…';
    });
    // Coming back with the browser's Back button restores a disabled button.
    window.addEventListener('pageshow', () => { btn.disabled = false; btn.innerHTML = btnHtml; });
  })();
</script>
<?php endif; ?>

<?= $this->endSection() ?>
