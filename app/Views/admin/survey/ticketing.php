<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\TicketingSurvey $config
 * @var array  $sections
 * @var array  $questions
 * @var array  $summary   SurveyService::summarise() output
 * @var array  $responses newest first, answers decoded
 * @var string $publicUrl
 * @var string $base
 */
$total = count($responses);
$pct   = static fn(int $n, int $of): int => $of > 0 ? (int) round($n / $of * 100) : 0;

$quoteName = static fn(array $t): string => $t['anonymous'] ? $t['name'] . ' · not for quoting by name' : $t['name'];

// Free-text and "Other" answers, rendered as quotes.
$quotes = static function (array $texts) use ($quoteName): string {
  $html = '';
  foreach ($texts as $t) {
    $html .= '<blockquote class="svy-quote mb-2"><div>' . esc($t['text']) . '</div><footer>'
      . esc($quoteName($t)) . ' · <a href="#resp-' . (int) $t['id'] . '">response #' . (int) $t['id'] . '</a></footer></blockquote>';
  }

  return $html;
};

// One answer from one response, as text.
$answerText = static function (array $q, string $id, array $a): string {
  $v = $a[$id] ?? null;
  $parts = match ($q['type']) {
    'single', 'scale' => $v !== null ? [$q['options'][$v] ?? $v] : [],
    'multi'           => array_map(static fn($k) => $q['options'][$k] ?? $k, (array) $v),
    'grid'            => [],
    default           => $v !== null ? [$v] : [],
  };
  if (! empty($a[$id . '_other'])) {
    $parts[] = 'Other: ' . $a[$id . '_other'];
  }

  return implode(' · ', $parts);
};

$features = null;
foreach ($summary as $id => $item) {
  if ($item['question']['type'] === 'grid') {
    $features = ['id' => $id] + $item;
    break;
  }
}
$columns = $features['question']['columns'] ?? [];
?>

<section class="hero-lcnl-watermark hero-overlay-ocean d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-3">
    <h1 class="fw-bold display-6 mb-2">Ticketing questionnaire</h1>
    <p class="lead fs-5 mb-0"><?= $total ?> response<?= $total === 1 ? '' : 's' ?> from committee members
      <?= $config->isOpen() ? '' : '&mdash; closed' ?></p>
  </div>
</section>

<div class="container py-4">

  <?php if ($m = session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($m) ?></div>
  <?php endif; ?>
  <?php if ($m = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($m) ?></div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
      <div class="flex-grow-1">
        <div class="small text-muted mb-1">Link to share with committee members (unlisted, not in search results)</div>
        <div class="input-group">
          <input type="text" class="form-control" value="<?= esc($publicUrl, 'attr') ?>" id="surveyLink" readonly>
          <button class="btn btn-outline-secondary" type="button"
            onclick="navigator.clipboard.writeText(document.getElementById('surveyLink').value).then(() => { this.textContent = 'Copied'; })">Copy</button>
          <a class="btn btn-outline-secondary" href="<?= esc($publicUrl, 'attr') ?>" target="_blank" rel="noopener">Open</a>
        </div>
      </div>
      <a href="<?= base_url($base . '/export') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-download me-1"></i> Export CSV
      </a>
    </div>
  </div>

  <?php if ($total === 0): ?>
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No responses yet. Share the link above with committee members.
      </div>
    </div>
  <?php else: ?>

    <ul class="nav nav-pills mb-4" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-summary" type="button" role="tab">Summary</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-responses" type="button" role="tab">Responses (<?= $total ?>)</button>
      </li>
    </ul>

    <div class="tab-content">
      <!-- ===================== Summary ===================== -->
      <div class="tab-pane fade show active" id="tab-summary" role="tabpanel">

        <?php if ($features): ?>
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
              <h2 class="h5 fw-bold mb-1">Feature priorities</h2>
              <p class="small text-muted mb-3">
                Ranked by score: <em>Must have</em> = 2, <em>Nice to have</em> = 1, <em>Not needed</em> = 0, averaged over
                everyone who gave a view (<em>Not sure</em> is left out). Above 1.5 is a clear requirement for the first release.
              </p>
              <div class="small mb-2">
                <?php foreach ($columns as $colId => $col): ?>
                  <span class="me-3"><span class="svy-key is-<?= esc($colId, 'attr') ?>"></span><?= esc($col['label']) ?></span>
                <?php endforeach; ?>
              </div>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                  <thead>
                    <tr>
                      <th scope="col">Feature</th>
                      <th scope="col" class="text-end">Score</th>
                      <?php foreach ($columns as $col): ?>
                        <th scope="col" class="text-end small"><?= esc($col['label']) ?></th>
                      <?php endforeach; ?>
                      <th scope="col" style="width:28%">Split</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($features['rows'] as $row): ?>
                      <tr>
                        <td><?= esc($row['label']) ?></td>
                        <td class="text-end fw-bold<?= ($row['score'] ?? 0) >= 1.5 ? ' text-success' : '' ?>">
                          <?= $row['score'] === null ? '–' : number_format($row['score'], 2) ?>
                        </td>
                        <?php foreach ($row['counts'] as $n): ?>
                          <td class="text-end"><?= $n ?></td>
                        <?php endforeach; ?>
                        <td>
                          <div class="svy-stack" title="<?= esc(implode(', ', array_map(
                            static fn($c, $n) => $columns[$c]['label'] . ': ' . $n, array_keys($row['counts']), $row['counts']
                          )), 'attr') ?>">
                            <?php foreach ($row['counts'] as $colId => $n): ?>
                              <?php if ($n > 0): ?>
                                <span class="is-<?= esc($colId, 'attr') ?>" style="width:<?= $pct($n, $row['total']) ?>%"></span>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php foreach ($sections as $section): ?>
          <?php
          // The grid is already shown at the top as "Feature priorities".
          $items = array_filter(
            array_intersect_key($summary, $section['questions']),
            static fn(array $i): bool => $i['question']['type'] !== 'grid'
          );
          if ($items === []) continue;
          ?>
          <h2 class="h5 fw-bold text-brand mt-4 mb-3"><?= esc($section['title']) ?></h2>

          <?php foreach ($items as $id => $item): ?>
            <?php $q = $item['question']; ?>
            <div class="card border-0 shadow-sm mb-3">
              <div class="card-body">
                <h3 class="h6 fw-bold mb-1"><?= esc($q['label']) ?></h3>
                <p class="small text-muted mb-3">
                  <?= $item['answered'] ?> of <?= $total ?> answered<?= $q['type'] === 'multi' ? ' · tick all that apply, so totals exceed answers' : '' ?>
                </p>

                <?php if (isset($item['counts'])): ?>
                  <?php foreach ($item['counts'] as $value => $n): ?>
                    <div class="svy-bar">
                      <span><?= esc($q['options'][$value]) ?></span>
                      <span class="svy-bar-track"><span style="width:<?= $pct($n, $item['answered']) ?>%"></span></span>
                      <span class="svy-bar-n"><?= $n ?></span>
                    </div>
                  <?php endforeach; ?>
                  <?php if ($item['stale'] > 0): ?>
                    <p class="small text-muted mt-2 mb-0"><?= $item['stale'] ?> answer(s) for options no longer on the form.</p>
                  <?php endif; ?>
                  <?php if (! empty($item['other'])): ?>
                    <div class="mt-3">
                      <div class="small fw-semibold mb-2">Other</div>
                      <?= $quotes($item['other']) ?>
                    </div>
                  <?php endif; ?>
                <?php else: ?>
                  <?= $item['texts'] ? $quotes($item['texts']) : '<p class="text-muted small mb-0">No comments yet.</p>' ?>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </div>

      <!-- ===================== Responses ===================== -->
      <div class="tab-pane fade" id="tab-responses" role="tabpanel">
        <div class="accordion" id="responseList">
          <?php foreach ($responses as $r): ?>
            <?php $a = $r['answers']; ?>
            <div class="accordion-item" id="resp-<?= (int) $r['id'] ?>">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#resp-body-<?= (int) $r['id'] ?>">
                  <span class="me-auto">
                    <span class="fw-semibold"><?= esc($r['respondent_name']) ?></span>
                    <span class="small text-muted ms-2">
                      <?= esc($answerText($questions['committees'] ?? ['type' => 'text'], 'committees', $a)) ?>
                    </span>
                  </span>
                  <?php if ($r['quote_preference'] !== \Config\TicketingSurvey::QUOTE_NAMED): ?>
                    <span class="badge bg-secondary me-2">don’t quote by name</span>
                  <?php endif; ?>
                  <span class="small text-muted me-2">#<?= (int) $r['id'] ?> · <?= esc(date('j M H:i', strtotime($r['created_at']))) ?></span>
                </button>
              </h2>
              <div id="resp-body-<?= (int) $r['id'] ?>" class="accordion-collapse collapse">
                <div class="accordion-body">
                  <?php if (! empty($r['email'])): ?>
                    <p class="mb-3"><i class="bi bi-envelope me-1"></i><a href="mailto:<?= esc($r['email'], 'attr') ?>"><?= esc($r['email']) ?></a></p>
                  <?php endif; ?>
                  <dl class="row mb-0">
                    <?php foreach ($questions as $id => $q): ?>
                      <?php if (in_array($id, ['name', 'email'], true)) continue; ?>
                      <?php if ($q['type'] === 'grid'): ?>
                        <?php if (empty($a[$id])) continue; ?>
                        <dt class="col-md-4 small"><?= esc($q['label']) ?></dt>
                        <dd class="col-md-8 small">
                          <?php foreach ($q['columns'] as $colId => $col): ?>
                            <?php $picked = array_keys(array_filter($a[$id], static fn($c) => $c === $colId)); ?>
                            <?php if ($picked): ?>
                              <div class="mb-1"><strong><?= esc($col['label']) ?>:</strong>
                                <?= esc(implode('; ', array_map(static fn($k) => $q['rows'][$k] ?? $k, $picked))) ?></div>
                            <?php endif; ?>
                          <?php endforeach; ?>
                        </dd>
                      <?php else: ?>
                        <?php $text = $answerText($q, $id, $a); ?>
                        <?php if ($text === '') continue; ?>
                        <dt class="col-md-4 small"><?= esc($q['label']) ?></dt>
                        <dd class="col-md-8 small" style="white-space:pre-line"><?= esc($text) ?></dd>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </dl>
                  <div class="text-end mt-3">
                    <?= view('admin/_delete_button', [
                      'action'  => site_url($base . '/delete/' . (int) $r['id']),
                      'confirm' => 'Remove the response from ' . $r['respondent_name'] . '? Use this for test entries.',
                      'label'   => '<i class="bi bi-trash me-1"></i>Remove response',
                      'class'   => 'btn btn-sm btn-outline-danger',
                    ]) ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

  <?php endif; ?>
</div>

<?= $this->endSection() ?>
