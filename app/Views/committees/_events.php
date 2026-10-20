<?php
/**
 * Upcoming events for one committee, as a simple dated list.
 *
 * @var array  $events  rows from EventModel, soonest first
 * @var string $who     e.g. "Mahila" — used in the empty state
 */
?>
<?php if (!empty($events)): ?>
  <ul class="cmt-events">
    <?php foreach ($events as $event): ?>
      <?php $ts = strtotime($event['event_date']); ?>
      <li>
        <a href="<?= base_url('events/' . $event['id']) ?>" class="cmt-event">
          <span class="cmt-event-date" aria-hidden="true">
            <span class="cmt-event-day"><?= date('j', $ts) ?></span>
            <span class="cmt-event-month"><?= date('M', $ts) ?></span>
          </span>
          <span>
            <span class="cmt-event-title"><?= esc($event['title']) ?></span>
            <span class="cmt-event-when">
              <?= date('l j F Y', $ts) ?>
              <?php if (!empty($event['time_from'])): ?>
                · <?= date('g:ia', strtotime($event['time_from'])) ?>
              <?php endif; ?>
            </span>
          </span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <p class="cmt-empty">
    There are no <?= esc($who) ?> events coming up right now.
    <a href="<?= base_url('events') ?>">See all LCNL events</a>.
  </p>
<?php endif; ?>
