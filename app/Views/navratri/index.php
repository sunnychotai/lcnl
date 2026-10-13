<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
/** @var \Config\Navratri $config */
$today    = $config->today();
$tonight  = $config->tonight();
$streamId = $tonight ? $config->streamFor($tonight['date']) : null;
$fmt      = static fn(string $date, string $pattern = 'l j F'): string => (new DateTimeImmutable($date))->format($pattern);
$tel      = static fn(string $number): string => 'tel:' . preg_replace('/\D/', '', $number);
$phone    = static fn(string $number) => '<a href="' . esc($tel($number), 'attr') . '" class="nvr-phone">' . esc($number) . '</a>';
?>

<?php if ($tonight): ?>
<section class="hero-lcnl-watermark nvr-hero is-themed<?= $tonight['ink'] === '#fff' ? '' : ' is-light' ?> d-flex align-items-center justify-content-center"
  style="--nvr-day: <?= esc($tonight['swatch'], 'attr') ?>; --nvr-ink: <?= esc($tonight['ink'], 'attr') ?>">
<?php else: ?>
<section class="hero-lcnl-watermark nvr-hero d-flex align-items-center justify-content-center">
<?php endif; ?>
  <div class="container position-relative text-center py-4">
    <span class="nvr-eyebrow">
      <?php if ($tonight): ?>
        Tonight &middot; <?= esc($tonight['label']) ?> &middot; <?= esc($tonight['colour']) ?>
      <?php else: ?>
        LCNL presents
      <?php endif; ?>
    </span>
    <h1 class="fw-bold display-5 mb-2">Navratri Festival 2026</h1>
    <p class="lead fs-5 mb-0">Ten evenings of garba, dandia, live music and community at the Dhamecha Lohana Centre</p>

    <div class="mela-hero-pills">
      <span class="pill is-accent"><i class="bi bi-calendar-heart"></i> 11&ndash;19 October</span>
      <span class="pill"><i class="bi bi-moon-stars"></i> Sharad Poonam 24 October</span>
      <span class="pill"><i class="bi bi-geo-alt"></i> Dhamecha Lohana Centre</span>
    </div>
  </div>
</section>

<nav class="nvr-jump" aria-label="On this page">
  <div class="container">
    <ul>
      <?php if ($tonight): ?><li><a href="#live"><i class="bi bi-broadcast"></i> Watch live</a></li><?php endif; ?>
      <li><a href="#nights"><i class="bi bi-palette"></i> Colours</a></li>
      <li><a href="#tickets"><i class="bi bi-ticket-perforated"></i> Tickets</a></li>
      <li><a href="#parking"><i class="bi bi-car-front"></i> Parking</a></li>
      <li><a href="#music"><i class="bi bi-music-note-beamed"></i> Music</a></li>
      <li><a href="#competitions"><i class="bi bi-trophy"></i> Competitions</a></li>
      <li><a href="#aarti"><i class="bi bi-fire"></i> Aarti Yajman</a></li>
      <li><a href="#business"><i class="bi bi-shop"></i> Advertising &amp; stalls</a></li>
      <li><a href="#mahila"><i class="bi bi-flower1"></i> Mahila Garba</a></li>
      <li><a href="#kids"><i class="bi bi-balloon"></i> Kids Garba</a></li>
    </ul>
  </div>
</nav>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-10">

      <p class="lead nvr-intro">
        We are delighted to welcome you to the <strong>LCNL Navratri Festival 2026</strong>, taking place at the
        Dhamecha Lohana Centre (DLC) from <strong>Sunday 11 October to Monday 19 October 2026</strong>, followed by our
        special <strong>Sharad Poonam</strong> celebration on <strong>Saturday 24 October 2026</strong>.
      </p>
      <p>
        Join us for ten wonderful evenings of traditional garba, dandia, live music and community celebrations as we
        come together to celebrate this much-loved festival. Below you&rsquo;ll find everything you need to know about
        tickets, entertainment, competitions, colour themes, sponsorship opportunities and arrangements for attending.
      </p>

      <?php if ($tonight): ?>
        <!-- ============ LIVE STREAM ============ -->
        <section id="live" class="nvr-section nvr-live">
          <span class="badge bg-danger mb-2"><i class="bi bi-broadcast me-1" aria-hidden="true"></i>Live tonight</span>
          <h2 class="nvr-h2">
            <?= esc($tonight['label']) ?> &mdash; <?= esc($fmt($tonight['date'])) ?>
            <span class="nvr-chip" style="background: <?= esc($tonight['swatch'], 'attr') ?>; color: <?= esc($tonight['ink'], 'attr') ?>">
              <?= esc($tonight['colour']) ?>
            </span>
          </h2>

          <?php if ($streamId): ?>
            <p class="mb-3">
              Can&rsquo;t join us at the DLC? Watch tonight&rsquo;s garba live below, or on the
              <a href="<?= esc($config->youtubeChannel, 'attr') ?>" target="_blank" rel="noopener">LCNL YouTube channel</a>.
            </p>
            <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
              <iframe src="https://www.youtube-nocookie.com/embed/<?= esc($streamId, 'url') ?>"
                title="Navratri <?= esc($tonight['label'], 'attr') ?> live stream"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
            <p class="small text-muted mt-2 mb-0">
              If the stream hasn&rsquo;t started yet, the player will show a countdown.
            </p>
          <?php else: ?>
            <p class="mb-0">
              Tonight&rsquo;s live stream will appear here shortly. In the meantime, you can find it on the
              <a href="<?= esc($config->youtubeChannel, 'attr') ?>" target="_blank" rel="noopener">LCNL YouTube channel</a>.
            </p>
          <?php endif; ?>
        </section>
      <?php endif; ?>

      <!-- ============ NIGHT BY NIGHT ============ -->
      <section id="nights" class="nvr-section">
        <h2 class="nvr-h2"><i class="bi bi-palette-fill"></i> Night by night &amp; colour themes</h2>
        <p>
          Get into the festive spirit by joining in with our daily colour themes! We encourage everyone to dress in the
          designated colours and help create a vibrant atmosphere each evening.
        </p>

        <div class="nvr-nights">
          <?php foreach ($config->nights as $night):
            $isToday = $night['date'] === $today;
            $isPast  = $night['date'] < $today;
            $video   = $config->streamFor($night['date']);
          ?>
            <article class="nvr-night<?= $isToday ? ' is-today' : '' ?><?= $isPast ? ' is-past' : '' ?>">
              <div class="nvr-night-swatch" style="background: <?= esc($night['swatch'], 'attr') ?>; color: <?= esc($night['ink'], 'attr') ?>">
                <span class="nvr-night-label"><?= esc($night['label']) ?></span>
                <span class="nvr-night-colour"><?= esc($night['colour']) ?></span>
              </div>
              <div class="nvr-night-body">
                <div class="fw-bold"><?= esc($fmt($night['date'], 'D j M')) ?>
                  <?php if ($isToday): ?><span class="badge bg-danger ms-1">Tonight</span><?php endif; ?>
                </div>
                <?php if (! empty($night['soldOut'])): ?>
                  <span class="badge text-bg-dark">Sold out</span>
                <?php elseif (! $isPast): ?>
                  <span class="small text-muted">&pound;<?= $config->ticketPrice($night['date']) ?> per person</span>
                <?php endif; ?>
                <?php foreach ($night['events'] ?? [] as $event): ?>
                  <div class="nvr-night-event"><i class="bi bi-star-fill" aria-hidden="true"></i> <?= esc($event) ?></div>
                <?php endforeach; ?>
                <?php if ($video && ($isPast || $isToday)): ?>
                  <a class="nvr-night-watch" href="https://www.youtube.com/watch?v=<?= esc($video, 'url') ?>" target="_blank" rel="noopener">
                    <i class="bi bi-youtube" aria-hidden="true"></i> <?= $isToday ? 'Watch live' : 'Watch again' ?>
                  </a>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>

        <p class="mt-3 mb-0">
          Whether you prefer traditional attire or something more contemporary, we&rsquo;d love to see everyone
          embracing the colours of Navratri.
        </p>
      </section>

      <!-- ============ TICKETS ============ -->
      <section id="tickets" class="nvr-section">
        <h2 class="nvr-h2"><i class="bi bi-ticket-perforated-fill"></i> Tickets &amp; admission</h2>
        <p>
          <strong>Tickets are now on general sale!</strong> With just <strong>800 public tickets</strong> available per
          evening, we encourage everyone to purchase their tickets early, particularly for weekends, which are always
          extremely popular.
        </p>

        <div class="alert alert-warning d-flex gap-2 align-items-start" role="note">
          <i class="bi bi-exclamation-triangle-fill mt-1" aria-hidden="true"></i>
          <div><strong>Friday 16 October</strong> and <strong>Saturday 17 October</strong> are now
            <strong>SOLD OUT</strong> for individual evening tickets.</div>
        </div>

        <div class="row g-3 my-1">
          <div class="col-md-4">
            <div class="nvr-price">
              <span class="nvr-price-amount">&pound;10</span>
              <span class="nvr-price-label">Friday, Saturday &amp; Sunday</span>
              <span class="small">per person</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="nvr-price">
              <span class="nvr-price-amount">&pound;5</span>
              <span class="nvr-price-label">Monday to Thursday</span>
              <span class="small">per person</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="nvr-price is-featured">
              <span class="nvr-price-amount">&pound;60</span>
              <span class="nvr-price-label">Season Pass</span>
              <span class="small">per person</span>
            </div>
          </div>
        </div>

        <p>
          We have deliberately kept ticket prices affordable so that as many people as possible can enjoy the
          celebrations. <strong>All tickets must be purchased and collected in advance.</strong> Due to limited capacity,
          please do not rely on purchasing tickets at the door.
        </p>

        <h3 class="nvr-h3">Where to buy tickets</h3>
        <p>Please contact your nearest ticket representative to arrange payment and collection.</p>
        <div class="table-responsive">
          <table class="table nvr-table align-middle">
            <thead>
              <tr><th scope="col">Contact</th><th scope="col">Area</th><th scope="col">Telephone</th></tr>
            </thead>
            <tbody>
              <tr><td>Dhiru Savani</td><td>Kingsbury</td><td><?= $phone('07956 492 825') ?></td></tr>
              <tr><td>Jeet Rughani</td><td>Ruislip</td><td><?= $phone('07956 284 177') ?></td></tr>
              <tr><td>Dinesh Shonchhatra</td><td>Stanmore</td><td><?= $phone('07956 810 647') ?></td></tr>
              <tr><td>Ronak Paw</td><td>Elstree</td><td><?= $phone('07803 589 429') ?></td></tr>
            </tbody>
          </table>
        </div>
        <p class="small text-muted">
          <strong>Important:</strong> these representatives sell tickets for the main evening garba celebrations only.
          Tickets for the Mahila Garba and YLS Kids Garba are organised separately.
        </p>

        <div class="nvr-callout">
          <h3 class="nvr-h3 mt-0"><i class="bi bi-stars"></i> Season Pass &ndash; &pound;60</h3>
          <p>Enjoy the complete LCNL Navratri experience with our &pound;60 Season Pass, providing admission to:</p>
          <ul>
            <li>All nine nights of Navratri, from 11&ndash;19 October 2026</li>
            <li>Sharad Poonam Garba on Saturday 24 October 2026</li>
          </ul>
          <p class="small">The Season Pass does not include the Mahila Garba or YLS Kids Garba events.</p>
          <p class="mb-0">To purchase a Season Pass, please contact <strong>Ronak Paw</strong> on <?= $phone('07803 589 429') ?>.</p>
        </div>
      </section>

      <!-- ============ PARKING ============ -->
      <section id="parking" class="nvr-section">
        <h2 class="nvr-h2"><i class="bi bi-car-front-fill"></i> Parking &amp; travel</h2>
        <p>
          Parking at the Dhamecha Lohana Centre is <strong>extremely limited</strong>, and we strongly encourage visitors
          to car share or use public transport wherever possible. Due to redevelopment works at Waitrose, normal
          parking arrangements are no longer available.
        </p>

        <h3 class="nvr-h3">Important parking guidelines</h3>
        <ol class="nvr-list">
          <li>Limited parking at Waitrose may be available <strong>after 9:00pm only</strong>, subject to availability and the instructions of our security team.</li>
          <li>Security staff will direct authorised vehicles and issue parking passes where available.</li>
          <li><strong>Do not park in the Waitrose car park</strong> without permission from security.</li>
          <li>Do not park on the slip road, in disabled bays or in electric vehicle charging bays unless specifically authorised by security.</li>
          <li>Once all parking passes have been allocated, visitors will need to find alternative permitted parking.</li>
          <li>Please <strong>do not park on Stanley Road</strong>, out of consideration for our neighbours.</li>
          <li>Pay-and-display parking is available at <strong>South Harrow Station</strong>. Please ensure you pay the appropriate parking charges and comply with all displayed restrictions.</li>
        </ol>
        <p class="small text-muted">
          All visitors are responsible for checking parking signs and restrictions. LCNL cannot accept responsibility or
          liability for parking fines, damage or injury.
        </p>
        <p class="mb-0">We appreciate everyone&rsquo;s cooperation in helping us minimise disruption to the local community.</p>
      </section>

      <!-- ============ MUSIC ============ -->
      <section id="music" class="nvr-section">
        <h2 class="nvr-h2"><i class="bi bi-music-note-beamed"></i> Live music &amp; entertainment</h2>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="nvr-card nvr-card-orange h-100">
              <span class="nvr-card-kicker">All nine nights &middot; 11&ndash;19 Oct</span>
              <h3 class="h5 fw-bold">Strings with Anuradha, Kiran &amp; the team</h3>
              <p class="mb-0">We are delighted to welcome back Strings, who will provide live music throughout all nine nights of Navratri.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="nvr-card nvr-card-purple h-100">
              <span class="nvr-card-kicker">Sharad Poonam &middot; Sat 24 Oct</span>
              <h3 class="h5 fw-bold">Rajvi Raja Entertainment with Nikesh Parmar</h3>
              <p class="mb-0">An energetic evening of Disco Dandia for our special Sharad Poonam celebration.</p>
            </div>
          </div>
        </div>
        <p class="mt-3 mb-0">We look forward to another fantastic year of music, dancing and celebrations!</p>
      </section>

      <!-- ============ COMPETITIONS ============ -->
      <section id="competitions" class="nvr-section">
        <h2 class="nvr-h2"><i class="bi bi-trophy-fill"></i> Garba, Dandia &amp; Best Dressed competitions</h2>
        <p>Our popular competitions return for 2026, giving participants the opportunity to showcase their dancing skills and festive style.</p>

        <div class="row g-3">
          <?php foreach ([
            ['Wed 14 Oct', 'Best Garba', 'Best Garba Male &middot; Best Garba Female', 'bi-person-arms-up'],
            ['Thu 15 Oct', 'Best Dandia', 'Best Dandia Male &middot; Best Dandia Female', 'bi-music-note'],
            ['Mon 19 Oct', 'Best Dressed', 'Best Dressed Male &middot; Best Dressed Female', 'bi-stars'],
            ['Sat 24 Oct', "President's Prize", 'During Sharad Poonam, the LCNL President will select a special recipient.', 'bi-award'],
          ] as [$when, $name, $detail, $icon]): ?>
            <div class="col-sm-6 col-lg-3">
              <div class="nvr-comp h-100">
                <i class="bi <?= $icon ?> nvr-comp-icon" aria-hidden="true"></i>
                <span class="nvr-comp-date"><?= $when ?></span>
                <h3 class="h6 fw-bold mb-1"><?= esc($name) ?></h3>
                <p class="small mb-0"><?= $detail ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <p class="small text-muted mt-3 mb-0">
          We extend our sincere thanks to Dhiru Savani, Jeet Rughani and Ronak Paw for generously sponsoring the competition prizes.
        </p>
      </section>

      <!-- ============ AARTI YAJMAN ============ -->
      <section id="aarti" class="nvr-section">
        <h2 class="nvr-h2"><i class="bi bi-fire"></i> Become an Aarti Yajman</h2>
        <p>
          We invite individuals and families to take part in the spiritual celebrations by becoming an Aarti Yajman
          during Navratri or Sharad Poonam. This is a wonderful opportunity to participate in the aarti ceremony
          alongside family and friends.
        </p>

        <div class="row g-4">
          <div class="col-md-6">
            <h3 class="nvr-h3 mt-0">Contributions</h3>
            <table class="table nvr-table align-middle">
              <thead><tr><th scope="col">Occasion</th><th scope="col" class="text-end">Contribution</th></tr></thead>
              <tbody>
                <tr><td>Fridays and Saturdays</td><td class="text-end fw-bold">&pound;501</td></tr>
                <tr><td>Sundays, Aatham and Sharad Poonam</td><td class="text-end fw-bold">&pound;251</td></tr>
                <tr><td>Mondays, Tuesdays and Wednesdays</td><td class="text-end fw-bold">&pound;151</td></tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-6">
            <h3 class="nvr-h3 mt-0">What&rsquo;s included?</h3>
            <ul class="nvr-ticks">
              <li>10 complimentary tickets for the evening you sponsor</li>
              <li>One aarti saani, which can be shared with your guests</li>
              <li>The opportunity to participate in the evening&rsquo;s aarti ceremony</li>
            </ul>
          </div>
        </div>

        <p>
          To ensure everyone has a comfortable experience, we can accommodate a <strong>maximum of five Aarti Yajmans
          per evening</strong>. Spaces are limited, so early booking is strongly recommended.
        </p>

        <div class="nvr-callout">
          <p class="fw-bold mb-2">For Aarti Yajman enquiries and bookings, please contact:</p>
          <ul class="mb-2">
            <li>Dinesh Shonchhatra &ndash; <?= $phone('07956 810 647') ?></li>
            <li>Dhirubhai Savani &ndash; <?= $phone('07956 492 825') ?></li>
            <li>Ronak Paw &ndash; <?= $phone('07803 589 429') ?></li>
          </ul>
          <p class="small mb-0">
            Aarti saanis will be provided by the organisers. Attendees should not bring their own aarti arrangements from home.
          </p>
        </div>
      </section>

      <!-- ============ BUSINESS ============ -->
      <section id="business" class="nvr-section">
        <h2 class="nvr-h2"><i class="bi bi-shop"></i> Business advertising &amp; sponsorship</h2>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="nvr-card nvr-card-blue h-100">
              <span class="nvr-card-kicker">On-screen advertising</span>
              <h3 class="h4 fw-bold">&pound;251</h3>
              <p>
                Promote your business to our Navratri audience through our large display screen positioned alongside
                the stage. The screen will feature important event announcements, aarti lyrics and sponsor
                advertisements throughout the celebrations.
              </p>
              <p>
                For &pound;251, your company logo or promotional image can be displayed at various points during each
                evening of garba.
              </p>
              <p class="small">This package is strictly for advertising purposes and does not include event tickets or participation in the aarti ceremony.</p>
              <p class="mb-0">Enquiries: <strong>Amit Karia</strong> &ndash; <?= $phone('07966 262 621') ?></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="nvr-card nvr-card-green h-100">
              <span class="nvr-card-kicker">Stallholders</span>
              <h3 class="h4 fw-bold">&pound;50 <small class="fs-6 fw-normal">per night</small> &middot; &pound;300 <small class="fs-6 fw-normal">full festival</small></h3>
              <p>
                A selection of stalls will be available in the <strong>Kantaria Hall (Blue Room)</strong> at the
                Dhamecha Lohana Centre. If you would like to promote your business, products or services during the
                festival, we welcome enquiries from prospective stallholders.
              </p>
              <p class="small">The full festival price covers the nine nights of Navratri and Sharad Poonam. Stall spaces are limited and advance booking is recommended.</p>
              <p class="mb-1">Enquiries:</p>
              <ul class="mb-0">
                <li>Dhiru Savani &ndash; <?= $phone('07956 492 825') ?></li>
                <li>Ronak Paw &ndash; <?= $phone('07803 589 429') ?></li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- ============ MAHILA ============ -->
      <section id="mahila" class="nvr-section">
        <h2 class="nvr-h2"><i class="bi bi-flower1"></i> Mahila Ladies Garba &ndash; Saturday 17 October</h2>
        <p>Our Mahila Ladies Garba will take place on Saturday 17 October, with a special <strong>Mela</strong> featuring stalls in the Kantaria Hall.</p>

        <div class="row g-3 my-1">
          <div class="col-4">
            <div class="nvr-price is-pink"><span class="nvr-price-amount">&pound;15</span><span class="nvr-price-label">Adults</span></div>
          </div>
          <div class="col-4">
            <div class="nvr-price is-pink"><span class="nvr-price-amount">&pound;5</span><span class="nvr-price-label">Children 5&ndash;10</span></div>
          </div>
          <div class="col-4">
            <div class="nvr-price is-pink"><span class="nvr-price-amount">Free</span><span class="nvr-price-label">Under 5</span></div>
          </div>
        </div>

        <p class="mb-0">
          Tickets are organised and sold separately by the <strong>LCNL Mahila team</strong>. Please refer to the Mahila
          communications for ticket purchasing information. Main evening garba ticket representatives do not sell
          tickets for this event.
        </p>
      </section>

      <!-- ============ KIDS ============ -->
      <section id="kids" class="nvr-section">
        <h2 class="nvr-h2"><i class="bi bi-balloon-fill"></i> YLS Kids Garba &ndash; Sundays 11 &amp; 18 October</h2>
        <p>Our younger community members can enjoy their own special Navratri celebrations at the YLS Kids Garba events.</p>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="nvr-chip nvr-chip-date"><i class="bi bi-calendar-event"></i> Sunday 11 October 2026</span>
          <span class="nvr-chip nvr-chip-date"><i class="bi bi-calendar-event"></i> Sunday 18 October 2026</span>
        </div>
        <p>
          Tickets must be <strong>purchased online in advance through YLS</strong>. For further details, please contact a
          member of the YLS team or refer to their event communications.
        </p>
        <p class="small text-muted mb-0">LCNL evening garba ticket representatives do not hold or sell Kids Garba tickets.</p>
      </section>

    </div>
  </div>
</div>

<?= $this->endSection() ?>
