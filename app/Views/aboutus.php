<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>


<!-- Hero Banner -->
<!-- Hero Banner -->
<div class="hero hero-rangoli-red d-flex align-items-center justify-content-center">
  <div class="overlay"></div>
  <div class="container position-relative">
    <h1 class="text-white fw-bold">About Us</h1>
    <p class="text-white-75">Lohana Community of North London</p>
  </div>
</div>



<!-- Page Content -->
<div class="container py-5">

<!-- Committee Image -->
<div class="mb-4 text-center">
    <!-- Small thumbnail, clickable -->
    <img src="<?= base_url('uploads/committee/lcnl-ec-25-27.JPG') ?>" 
         alt="LCNL Executive Committee"
         class="img-fluid rounded shadow committee-img"
         style="max-width: 100%;"
         data-bs-toggle="modal" data-bs-target="#committeeModal">
    <!-- Subtext caption -->
    <p class="mt-2 fw-semibold text-muted">LCNL Executive Committee 2025-7</p>
</div>




<p>The Lohana Community North London (LCNL) was founded in 1976 as an offshoot of the Lohana Union. Over the years, it has grown into a prominent voluntary organisation serving thousands of families across North London and Middlesex.</p>

<p>Our aim is to promote charitable causes, advance Hindu religion and culture, support education, and provide relief to those in need. We achieve this through our charitable trust, Mahila Mandal, Sports Club, Young Lohana Society, senior citizens’ groups, and a wide range of subcommittees.</p>

<p>LCNL connects with over 2,300 families through regular News & Events, the Raghuvanshi Diwali Magazine, and annual festivals. We come together for Navratri, Diwali, Janmashtami, Hanuman Jayanti, and many more religious and cultural celebrations.</p>

<p>We are proud of our contributions to local and international charities, the establishment of the RCT Sports Centre in Harrow, and most recently, the acquisition of a new community centre to serve future generations.</p>

<p>Rooted in a long tradition of unity and service, LCNL continues to move forward together, honouring our history while building for the future.</p>
<p>There are various affiliated commitees under the LCNL, these are:
    <ul>
        <li>Mahila Mandal</li>
        <li>Young Lohana Society</li>
        <li>Youth Committee</li>
        <li>Youth Committee</li>
        <li>Senior Mens</li>
        <li>Senior Ladies</li>
        <li>Raghuvanshi Charitable Trust</li>
        <li>Lohana Charity Foundation</li>
    </ul>
<div class="container text-left my-3">
    <blockquote class="blockquote">
        <p class="mb-0 fs-5 fw-bold text-brand">-- “We Move Forward Together”</p>
    </blockquote>
</div>
</div>

<?= $this->endSection() ?>
