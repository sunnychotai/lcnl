<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<section class="hero-lcnl-watermark hero-overlay-steel d-flex align-items-center justify-content-center">
  <div class="container position-relative text-center text-white py-4">
    <h1 class="fw-bold display-6 mb-1">
      <i class="bi bi-pencil-square me-2"></i>LCNL Golf Event 2026
    </h1>
    <p class="mb-0 opacity-75">Complete the form below to register your place(s)</p>
  </div>
</section>

<div class="container py-5">

  <?php if ($isFull): ?>
    <div class="alert alert-danger shadow-sm mb-4">
      <i class="bi bi-x-circle-fill me-2"></i>
      <strong>Event Full</strong> — Sorry, all <?= 40 ?> player spots have been filled.
      Please contact the organisers if you have any questions.
    </div>
  <?php elseif ($spotsRemaining <= 6): ?>
    <div class="alert alert-warning shadow-sm mb-4">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Almost full!</strong> Only <?= $spotsRemaining ?> player <?= $spotsRemaining === 1 ? 'spot' : 'spots' ?> remaining.
    </div>
  <?php endif; ?>

  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger shadow-sm alert-dismissible fade show">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Please correct the following:</strong>
      <ul class="mb-0 mt-2">
        <?php foreach ($errors as $err): ?>
          <li><?= esc($err) ?></li>
        <?php endforeach; ?>
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

      <form method="post" action="<?= site_url('golf/register') ?>" novalidate id="golfRegForm">
        <?= csrf_field() ?>
        <input type="hidden" name="form_time" value="<?= time() ?>">
        <input type="text" name="website" style="display:none;" tabindex="-1" autocomplete="off">

        <!-- ── Team Details ───────────────────────────────── -->
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-gradient py-3">
            <h4 class="mb-0">
              <i class="bi bi-people-fill me-2"></i>Team Details
            </h4>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-semibold" for="team_name">
                  Team Name <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="team_name" name="team_name"
                  value="<?= esc(old('team_name')) ?>"
                  placeholder="e.g. The Bogey Bandits" maxlength="100" required>
                <div class="form-text">Enter a name for your team or group.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- ── Player 1 ────────────────────────────────────── -->
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-gradient py-3">
            <h4 class="mb-0">
              <i class="bi bi-person-fill me-2"></i>Player 1
              <span class="badge bg-danger ms-2 small">Required</span>
            </h4>
          </div>
          <div class="card-body p-4">
            <?= view('golf/_player_fields', ['prefix' => 'p1', 'required' => true]) ?>
          </div>
        </div>

        <!-- ── Player 2 ────────────────────────────────────── -->
        <div class="card shadow-sm border-0 mb-4" id="p2Card">
          <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-muted">
              <i class="bi bi-person me-2"></i>Player 2
              <span class="badge bg-secondary ms-2 small">Optional</span>
            </h4>
            <button type="button" class="btn btn-sm btn-outline-brand rounded-pill"
              id="toggleP2" onclick="togglePlayer(2)">
              <i class="bi bi-plus-circle me-1" id="p2Icon"></i>
              <span id="p2BtnText">Add Player 2</span>
            </button>
          </div>
          <div class="card-body p-4" id="p2Fields" style="display:none;">
            <?= view('golf/_player_fields', ['prefix' => 'p2', 'required' => false]) ?>
          </div>
        </div>

        <!-- ── Player 3 ────────────────────────────────────── -->
        <div class="card shadow-sm border-0 mb-4" id="p3Card">
          <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-muted">
              <i class="bi bi-person me-2"></i>Player 3
              <span class="badge bg-secondary ms-2 small">Optional</span>
            </h4>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill"
              id="toggleP3" onclick="togglePlayer(3)" disabled>
              <i class="bi bi-plus-circle me-1" id="p3Icon"></i>
              <span id="p3BtnText">Add Player 3</span>
            </button>
          </div>
          <div class="card-body p-4" id="p3Fields" style="display:none;">
            <?= view('golf/_player_fields', ['prefix' => 'p3', 'required' => false]) ?>
          </div>
        </div>

        <!-- ── Terms ──────────────────────────────────────── -->
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-body p-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="agreed_terms" value="1"
                id="agreedTerms" <?= old('agreed_terms') == '1' ? 'checked' : '' ?> required>
              <label class="form-check-label" for="agreedTerms">
                I agree to the
                <a href="<?= site_url('golf') ?>#terms" target="_blank">event terms and conditions</a>
                and confirm that all player details provided are accurate.
              </label>
            </div>
          </div>
        </div>

        <!-- ── Submit ─────────────────────────────────────── -->
        <div class="d-grid">
          <button type="submit" class="btn btn-brand btn-lg rounded-pill"
            <?= $isFull ? 'disabled' : '' ?>>
            <i class="bi bi-send-check me-2"></i>Submit Registration
          </button>
        </div>

        <p class="text-center text-muted small mt-3">
          <i class="bi bi-lock me-1"></i>
          Your details are kept securely and will only be used to manage your registration.
        </p>

      </form>

    </div>
  </div>
</div>

<script>
  function togglePlayer(num) {
    const fields  = document.getElementById('p' + num + 'Fields');
    const btn     = document.getElementById('toggleP' + num);
    const icon    = document.getElementById('p' + num + 'Icon');
    const btnText = document.getElementById('p' + num + 'BtnText');
    const isOpen  = fields.style.display !== 'none';

    if (isOpen) {
      fields.style.display = 'none';
      icon.className       = 'bi bi-plus-circle me-1';
      btnText.textContent  = 'Add Player ' + num;
      // Clear fields when collapsing
      fields.querySelectorAll('input, select').forEach(el => el.value = '');
      if (num === 2) {
        // Also collapse player 3 if player 2 is removed
        const p3 = document.getElementById('p3Fields');
        if (p3 && p3.style.display !== 'none') togglePlayer(3);
        document.getElementById('toggleP3').disabled = true;
        document.getElementById('toggleP3').classList.replace('btn-outline-brand', 'btn-outline-secondary');
      }
    } else {
      fields.style.display = 'block';
      icon.className       = 'bi bi-dash-circle me-1';
      btnText.textContent  = 'Remove Player ' + num;
      if (num === 2) {
        const p3btn = document.getElementById('toggleP3');
        p3btn.disabled = false;
        p3btn.classList.replace('btn-outline-secondary', 'btn-outline-brand');
      }
    }
  }

  // Re-open sections if old() values are present (after validation failure)
  document.addEventListener('DOMContentLoaded', function () {
    <?php if (old('p2_first_name')): ?>
      togglePlayer(2);
    <?php endif; ?>
    <?php if (old('p3_first_name')): ?>
      togglePlayer(3);
    <?php endif; ?>
  });
</script>

<?= $this->endSection() ?>
