<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var \Config\TicketingSurvey $config
 * @var string $firstName
 */
?>

<?= view('partials/page_header', [
  'overlay'    => 'peacock',
  'eyebrow'    => 'For LCNL committee members',
  'heading'    => 'Ticketing and events questionnaire',
]) ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="mela-form-card">
        <div class="card-body p-4 p-md-5 text-center mela-closed">
          <i class="bi bi-check-circle"></i>
          <h2 class="h3 fw-bold mt-3">Thank you, <?= esc($firstName) ?></h2>
          <p class="mb-2">Your answers have been sent to the team planning LCNL’s online ticketing.</p>
          <p class="text-muted mb-0">
            Thought of something else? Email
            <a href="mailto:<?= esc($config->contactEmail, 'attr') ?>"><?= esc($config->contactEmail) ?></a>,
            or <a href="<?= esc(site_url($config->slug)) ?>">fill the questionnaire in again</a> &mdash;
            we will read both.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // The answers are in; drop the draft kept while filling in the form.
  try { localStorage.removeItem(<?= json_encode('lcnl-survey-' . $config->key) ?>); } catch (e) { /* ignore */ }
</script>

<?= $this->endSection() ?>
