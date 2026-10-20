<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * @var bool   $loggedIn
 * @var int    $memberId
 * @var string $memberName
 * @var bool   $isLife
 * @var string $memberStatus
 */
$benefits = [
  ['A vote at the AGM', 'Have your say on how LCNL is run, and an invitation to the Annual General Meeting each year.'],
  ['The Raghuvanshi Diwali Magazine', 'Our annual magazine, plus regular news about the community.'],
  ['First word on events', 'Hear about events early and get priority access to tickets.'],
  ['Every celebration, all year', 'Navratri, Diwali, Janmashtami, Hanuman Jayanti and our social, religious and cultural events.'],
];
?>

<?= view('partials/page_header', [
  'overlay'    => 'steel',
  'heading'    => 'Membership',
  'subheading' => 'Lohana Community North London, since 1976',
]) ?>

<div class="container py-4 py-md-5 mship">

  <?php if ($loggedIn): ?>
    <!-- ===== Signed in: where you stand ===== -->
    <section class="mship-status" aria-labelledby="mship-status-title">
      <div>
        <h2 id="mship-status-title" class="h4 fw-bold mb-1">Hello, <?= esc($memberName) ?></h2>
        <p class="mb-0 text-muted">
          Membership number <strong class="text-body">LCNL<?= esc($memberId) ?></strong>
          &middot; <?= $isLife ? 'Life Member' : 'Standard member' ?>
          <?php if ($memberStatus !== 'active'): ?>
            &middot; <span class="text-danger"><?= esc(ucfirst($memberStatus)) ?></span>
          <?php endif; ?>
        </p>
      </div>
      <a href="<?= base_url('account/dashboard') ?>" class="btn btn-outline-brand rounded-pill px-4">My dashboard</a>
    </section>
  <?php endif; ?>

  <!-- ===== What Life Membership is ===== -->
  <section class="row g-4 g-lg-5 align-items-start mb-5">
    <div class="col-lg-7">
      <?php if ($loggedIn && $isLife): ?>
        <h2 class="mship-title">Thank you for being a Life Member</h2>
        <p class="mship-lead">
          Your membership supports everything LCNL does for over 2,300 families across North London and
          Middlesex. Here’s what it includes.
        </p>
      <?php else: ?>
        <h2 class="mship-title">Belong to LCNL for life</h2>
        <p class="mship-lead">
          Since 1976, LCNL has brought Lohana families in North London and Middlesex together through our
          festivals, our charitable work and our committees. Life Membership is how you belong formally,
          with a say in how LCNL is run.
        </p>
      <?php endif; ?>

      <h3 class="mship-subtitle"><?= $loggedIn && $isLife ? 'Your membership includes' : 'What Life Members get' ?></h3>
      <ul class="mship-benefits">
        <?php foreach ($benefits as [$title, $body]): ?>
          <li>
            <strong><?= esc($title) ?></strong>
            <span><?= esc($body) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <?php if (!($loggedIn && $isLife)): ?>
      <div class="col-lg-5">
        <aside class="mship-price" aria-labelledby="mship-price-title">
          <h2 id="mship-price-title" class="visually-hidden">Life Membership price</h2>
          <p class="mship-price-amount">£75</p>
          <p class="mship-price-term">One payment, for life. No renewals.</p>

          <?php if ($loggedIn): ?>
            <form action="<?= route_to('account.membership.upgrade.checkout') ?>" method="post"
              onsubmit="this.querySelector('button').disabled = true;">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-brand btn-lg rounded-pill w-100">Pay £75 by card</button>
            </form>
            <p class="mship-price-note">
              Secure payment by Stripe. Prefer bank transfer?
              <a href="<?= base_url('account/dashboard') ?>">Your dashboard has our details</a>.
            </p>
          <?php else: ?>
            <a href="<?= base_url('membership/register') ?>" class="btn btn-brand btn-lg rounded-pill w-100">Join LCNL</a>
            <p class="mship-price-note">
              Already a member? <a href="<?= base_url('membership/login') ?>">Log in</a>
            </p>

            <h3 class="mship-steps-title">How joining works</h3>
            <ol class="mship-steps">
              <li><strong>Register.</strong> It’s free and takes about two minutes.</li>
              <li><strong>Confirm your email</strong> using the link we send you.</li>
              <li><strong>Pay £75</strong> by card or bank transfer from your dashboard.</li>
            </ol>
          <?php endif; ?>
        </aside>
      </div>
    <?php endif; ?>
  </section>

  <?php if (!$loggedIn): ?>
    <!-- ===== Existing members ===== -->
    <section class="mship-existing mb-5" aria-labelledby="mship-existing-title">
      <h2 id="mship-existing-title" class="mship-subtitle mt-0">Already a member?</h2>
      <p class="mb-2">
        If you joined LCNL before this website, you’re probably already on our list. You don’t need to register again:
        <a href="<?= base_url('membership/forgot') ?>">set a password</a> using the email address we have for you, then
        <a href="<?= base_url('membership/login') ?>">log in</a>.
      </p>
      <p class="mb-0 text-muted">
        Not sure which email we have? Ask <a href="mailto:membership@lcnl.org">membership@lcnl.org</a>.
      </p>
    </section>

    <!-- ===== Questions ===== -->
    <section class="mship-faq mb-4" aria-labelledby="mship-faq-title">
      <h2 id="mship-faq-title" class="mship-subtitle mt-0">Questions</h2>

      <details>
        <summary>What’s the difference between registering and Life Membership?</summary>
        <p>
          Registering creates your free online account, so you can keep your details up to date and hear about events.
          Life Membership is the £75 one-off payment that gives you a vote at the AGM, the Diwali Magazine and priority
          for event tickets. You can register now and become a Life Member whenever you’re ready.
        </p>
      </details>

      <details>
        <summary>Can I pay by bank transfer?</summary>
        <p>
          Yes. Once you’ve registered and logged in, your dashboard shows our bank details and your own payment
          reference. Bank transfers are checked by hand, so allow a few days for your membership to update.
        </p>
      </details>

      <details>
        <summary>Can I add my family?</summary>
        <p>Yes. Once you’re logged in, you can add family members to your account from your dashboard.</p>
      </details>

      <details>
        <summary>Who do I ask if I’m stuck?</summary>
        <p>
          Email our membership team at <a href="mailto:membership@lcnl.org">membership@lcnl.org</a> and we’ll help.
        </p>
      </details>
    </section>
  <?php endif; ?>

</div>

<?= $this->endSection() ?>
