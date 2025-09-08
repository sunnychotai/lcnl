<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>


<!-- Hero Banner -->
<div class="position-relative w-100" style="height: 400px; overflow: hidden;">
    <img src="<?= base_url('assets/img/site/lcnl-ec-2025.jpg') ?>" 
         alt="LCNL Executive Committee 2025" 
         class="img-fluid w-100 h-100" 
         style="object-fit: cover; object-position: top;">

    <!-- Semi-transparent dark layer -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.4);"></div>

<!-- Overlay text -->
<div class="position-absolute top-50 start-0 translate-middle-y w-100 text-white">
    <div class="container">
        <h1 class="fw-bold display-4">About Us</h1>
        <p class="lead">Lohana Community of North London</p>
    </div>
</div>

</div>



<!-- Page Content -->
<div class="container py-5">
    <h1 class="mb-4">About Us</h1>
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
<div class="container text-left my-5">
    <blockquote class="blockquote">
        <p class="mb-0 fs-5 fw-bold text-brand">“We Move Forward Together”</p>
    </blockquote>
</div>
</div>

<?= $this->endSection() ?>
