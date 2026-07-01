<?php
// Variables available: $prefix (p1/p2/p3), $required (bool)
$req = $required ? 'required' : '';
?>

<div class="row g-3">

  <div class="col-sm-6">
    <label class="form-label fw-semibold" for="<?= $prefix ?>_first_name">
      First Name <?= $required ? '<span class="text-danger">*</span>' : '' ?>
    </label>
    <input type="text" class="form-control" id="<?= $prefix ?>_first_name"
      name="<?= $prefix ?>_first_name"
      value="<?= esc(old($prefix . '_first_name')) ?>"
      placeholder="First name" <?= $req ?>>
  </div>

  <div class="col-sm-6">
    <label class="form-label fw-semibold" for="<?= $prefix ?>_last_name">
      Surname <?= $required ? '<span class="text-danger">*</span>' : '' ?>
    </label>
    <input type="text" class="form-control" id="<?= $prefix ?>_last_name"
      name="<?= $prefix ?>_last_name"
      value="<?= esc(old($prefix . '_last_name')) ?>"
      placeholder="Surname" <?= $req ?>>
  </div>

  <div class="col-sm-6">
    <label class="form-label fw-semibold" for="<?= $prefix ?>_email">
      Email Address <?= $required ? '<span class="text-danger">*</span>' : '' ?>
    </label>
    <input type="email" class="form-control" id="<?= $prefix ?>_email"
      name="<?= $prefix ?>_email"
      value="<?= esc(old($prefix . '_email')) ?>"
      placeholder="name@example.com" <?= $req ?>>
    <div class="form-text">Confirmation email will be sent to this address.</div>
  </div>

  <div class="col-sm-6">
    <label class="form-label fw-semibold" for="<?= $prefix ?>_phone">
      Telephone <?= $required ? '<span class="text-danger">*</span>' : '' ?>
    </label>
    <input type="tel" class="form-control" id="<?= $prefix ?>_phone"
      name="<?= $prefix ?>_phone"
      value="<?= esc(old($prefix . '_phone')) ?>"
      placeholder="07xxx xxxxxx" <?= $req ?>>
  </div>

  <div class="col-sm-6">
    <label class="form-label fw-semibold" for="<?= $prefix ?>_handicap">
      Golf Handicap <?= $required ? '<span class="text-danger">*</span>' : '' ?>
    </label>
    <input type="number" class="form-control" id="<?= $prefix ?>_handicap"
      name="<?= $prefix ?>_handicap"
      value="<?= esc(old($prefix . '_handicap')) ?>"
      placeholder="e.g. 18" min="-10" max="54" step="0.1" <?= $req ?>>
    <div class="form-text">Enter your current playing handicap (0&ndash;54).</div>
  </div>

  <div class="col-sm-6">
    <label class="form-label fw-semibold" for="<?= $prefix ?>_meal">
      Meal Preference <?= $required ? '<span class="text-danger">*</span>' : '' ?>
    </label>
    <select class="form-select" id="<?= $prefix ?>_meal"
      name="<?= $prefix ?>_meal" <?= $req ?>>
      <option value="" disabled <?= old($prefix . '_meal') ? '' : 'selected' ?>>
        Select preference
      </option>
      <option value="non_vegetarian"
        <?= old($prefix . '_meal') === 'non_vegetarian' ? 'selected' : '' ?>>
        Non-Vegetarian
      </option>
      <option value="vegetarian"
        <?= old($prefix . '_meal') === 'vegetarian' ? 'selected' : '' ?>>
        Vegetarian
      </option>
    </select>
  </div>


</div>
