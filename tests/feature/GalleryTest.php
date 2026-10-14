<?php

namespace Tests\Feature;

use App\Models\GalleryAlbumModel;
use App\Models\GalleryPhotoModel;
use App\Services\GalleryService;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use Tests\Support\Libraries\FakeR2Client;

/**
 * @internal
 */
final class GalleryTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // The app's older migrations use MySQL-only SQL, so build just the tables
    // these tests touch on the in-memory SQLite test database.
    protected $migrate = false;
    protected $refresh = false;

    private const ADMIN = ['isAdminLoggedIn' => true, 'admin_role' => 'ADMIN', 'admin_name' => 'Test Admin'];

    private FakeR2Client $r2;

    /** @var list<string> */
    private array $tempFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF is covered sitewide; these tests are about the controllers.
        $filters = config('Filters');
        unset($filters->globals['before']['csrf']);
        Factories::injectMock('config', 'Filters', $filters);

        $this->r2 = new FakeR2Client();
        Services::injectMock('r2', $this->r2);

        $forge = \Config\Database::forge($this->DBGroup);

        foreach (['gallery_photos', 'gallery_albums', 'events', 'event_registrations'] as $table) {
            $forge->dropTable($table, true);
        }

        require_once APPPATH . 'Database/Migrations/2026-10-14-120000_CreateGalleryAlbums.php';
        require_once APPPATH . 'Database/Migrations/2026-10-14-120001_CreateGalleryPhotos.php';
        (new \App\Database\Migrations\CreateGalleryAlbums($forge))->up();
        (new \App\Database\Migrations\CreateGalleryPhotos($forge))->up();
        require_once APPPATH . 'Database/Migrations/2026-10-14-180000_AddWatermarkedToGalleryPhotos.php';
        (new \App\Database\Migrations\AddWatermarkedToGalleryPhotos($forge))->up();

        $text = ['type' => 'TEXT', 'null' => true];

        $forge->addField(['id' => ['type' => 'INTEGER', 'auto_increment' => true]] + array_fill_keys([
            'title', 'slug', 'description', 'event_date', 'location', 'time_from', 'time_to', 'image',
            'committee', 'ticketinfo', 'purchase_ticket_url', 'ticket_url_label', 'eventterms', 'contactinfo',
            'requires_registration', 'registration_open', 'max_registrations', 'max_headcount',
            'is_valid', 'is_sold_out', 'created_at', 'updated_at',
        ], $text));
        $forge->addKey('id', true);
        $forge->createTable('events');
    }

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    private function album(array $overrides = []): int
    {
        $model = new GalleryAlbumModel();

        return $model->insert(array_merge([
            'title'        => 'Navratri 2026',
            'slug'         => 'navratri-2026',
            'album_date'   => '2026-10-03',
            'credit'       => 'Photos by NiteshTakePhotos for LCNL',
            'is_published' => 1,
        ], $overrides), true);
    }

    /** A real JPEG on disk, as the browser would upload it. */
    private function jpeg(int $width, int $height, int $colour = 0x7A1D3C): string
    {
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, $colour);
        $path = tempnam(sys_get_temp_dir(), 'gal') . '.jpg';
        imagejpeg($image, $path, 85);
        $this->tempFiles[] = $path;

        return $path;
    }

    private function addPhoto(int $albumId, int $n, int $width = 1200, int $height = 800): array
    {
        $result = (new GalleryService())->addPhoto(
            $albumId,
            $this->jpeg($width, $height, $n * 4000),
            hash('sha256', "photo-{$n}"),
            "DSC_{$n}.jpg",
            $n,
            'Test Admin',
        );

        return $result['photo'] ?? [];
    }

    // -----------------------------------------------------------------
    // Access
    // -----------------------------------------------------------------

    public function testAdminPagesAreAdminOnly(): void
    {
        $this->get('admin/gallery')->assertRedirect();

        $this->withSession(['isAdminLoggedIn' => true, 'admin_role' => 'EVENTS'])
            ->get('admin/gallery')
            ->assertRedirectTo('/admin/system/dashboard');

        $result = $this->withSession(self::ADMIN)->get('admin/gallery');
        $result->assertOK();
        $result->assertSee('Photo Gallery');
    }

    public function testAdminScreensRender(): void
    {
        $albumId = $this->album();
        $this->addPhoto($albumId, 1);

        $this->withSession(self::ADMIN)->get('admin/gallery/create')->assertSee('Photos by NiteshTakePhotos for LCNL');
        $this->withSession(self::ADMIN)->get("admin/gallery/edit/{$albumId}")->assertSee('Save details');

        $album = $this->withSession(self::ADMIN)->get("admin/gallery/album/{$albumId}");
        $album->assertOK();
        $album->assertSee('Drag photos or a whole folder here');
        $album->assertSee('DSC_1.jpg');
        $album->assertSee('Cover');
    }

    public function testCreatingAlbumsGivesUniqueSlugs(): void
    {
        $post = ['title' => 'Diwali 2026', 'album_date' => '2026-11-08', 'credit' => 'Photos by X'];

        $this->withSession(self::ADMIN)->post('admin/gallery/store', $post)->assertRedirect();
        $this->withSession(self::ADMIN)->post('admin/gallery/store', $post)->assertRedirect();

        $slugs = array_column((new GalleryAlbumModel())->orderBy('id')->findAll(), 'slug');
        $this->assertSame(['diwali-2026', 'diwali-2026-2'], $slugs);

        $album = (new GalleryAlbumModel())->first();
        $this->assertSame('0', (string) $album['is_published'], 'New albums start as drafts.');
        $this->assertSame('Test Admin', $album['created_by']);
    }

    // -----------------------------------------------------------------
    // Uploading and storage
    // -----------------------------------------------------------------

    public function testUploadStoresResizedWebpVariantsAndSkipsDuplicates(): void
    {
        $albumId = $this->album();
        $photo   = $this->addPhoto($albumId, 1, 3000, 2000);

        $this->assertSame(1600, (int) $photo['width']);
        $this->assertSame(1067, (int) $photo['height']);
        $this->assertCount(2, $this->r2->objects);

        $web   = $this->r2->objects[$photo['storage_key'] . '-web.webp'];
        $thumb = $this->r2->objects[$photo['storage_key'] . '-thumb.webp'];
        $this->assertSame('image/webp', $web['type']);
        $this->assertStringContainsString('immutable', $web['cache']);
        $this->assertSame([600, 400], array_slice(getimagesizefromstring($thumb['body']), 0, 2));

        $album = (new GalleryAlbumModel())->find($albumId);
        $this->assertSame(1, (int) $album['photo_count']);
        $this->assertSame((int) $photo['id'], (int) $album['cover_photo_id']);

        // Same original again: skipped, nothing new stored.
        $again = (new GalleryService())->addPhoto($albumId, $this->jpeg(3000, 2000), hash('sha256', 'photo-1'), 'copy.jpg', 9, null);
        $this->assertSame('duplicate', $again['status']);
        $this->assertCount(2, $this->r2->objects);
    }

    /** RGB at a point of a stored WebP. */
    private function pixel(string $key, int $x, int $y): array
    {
        $image = imagecreatefromstring($this->r2->objects[$key]['body']);
        $c     = imagecolorsforindex($image, imagecolorat($image, $x, $y));

        return [$c['red'], $c['green'], $c['blue']];
    }

    private function assertColourNear(array $expected, array $actual, int $tolerance = 12): void
    {
        foreach ($expected as $i => $value) {
            $this->assertEqualsWithDelta($value, $actual[$i], $tolerance, 'Colour ' . json_encode($actual) . ' is not near ' . json_encode($expected));
        }
    }

    public function testWatermarkGoesTopRightOnTheWebImageOnly(): void
    {
        $albumId = $this->album();
        $photo   = $this->addPhoto($albumId, 1); // 1200x800, solid colour 4000 = rgb(0, 15, 160)
        $plain   = [0, 15, 160];

        $this->assertSame(1, (int) $photo['watermarked']);

        // Logo is 14% of the short side (112px) with a 22px margin: its centre
        // is at (1122, 78) on the web image and (561, 39) on the thumbnail.
        $marked = $this->pixel($photo['storage_key'] . '-web.webp', 1122, 78);
        $this->assertGreaterThan(80, $marked[0], 'Expected the logo\'s maroon centre, got ' . json_encode($marked));

        $this->assertColourNear($plain, $this->pixel($photo['storage_key'] . '-web.webp', 600, 400));
        $this->assertColourNear($plain, $this->pixel($photo['storage_key'] . '-thumb.webp', 561, 39));
    }

    public function testPhotosUploadedBeforeTheWatermarkCanBeMarkedLater(): void
    {
        $config            = config('Gallery');
        $config->watermark = false;
        Factories::injectMock('config', 'Gallery', $config);

        $albumId = $this->album();
        $before  = $this->addPhoto($albumId, 1);
        $this->assertSame(0, (int) $before['watermarked']);
        $this->assertColourNear([0, 15, 160], $this->pixel($before['storage_key'] . '-web.webp', 1122, 78));

        $config->watermark = true;

        $this->withSession(self::ADMIN)->get("admin/gallery/album/{$albumId}")
            ->assertSee('1 photo was uploaded before the LCNL watermark was added.');

        $result = $this->withSession(self::ADMIN)->post("admin/gallery/album/{$albumId}/watermark");
        $result->assertOK();
        $body = json_decode($result->getJSON(), true);
        $this->assertSame([1, 0, 0], [$body['done'], $body['failed'], $body['remaining']]);

        $after = (new GalleryPhotoModel())->find($before['id']);
        $this->assertSame(1, (int) $after['watermarked']);
        $this->assertNotSame($before['storage_key'], $after['storage_key'], 'A new key, so the cached unmarked image is never served for it.');
        $this->assertSame([$after['storage_key'] . '-web.webp', $after['storage_key'] . '-thumb.webp'], array_keys($this->r2->objects));
        $this->assertGreaterThan(80, $this->pixel($after['storage_key'] . '-web.webp', 1122, 78)[0]);

        $this->withSession(self::ADMIN)->get("admin/gallery/album/{$albumId}")
            ->assertDontSee('uploaded before the LCNL watermark');
    }

    public function testCheckEndpointReportsPhotosAlreadyInTheAlbum(): void
    {
        $albumId = $this->album();
        $this->addPhoto($albumId, 1);

        $result = $this->withSession(self::ADMIN)->post("admin/gallery/album/{$albumId}/check", [
            'hashes' => [hash('sha256', 'photo-1'), hash('sha256', 'photo-2'), 'not-a-hash'],
        ]);

        $result->assertOK();
        $this->assertSame([hash('sha256', 'photo-1')], json_decode($result->getJSON(), true)['existing']);
    }

    public function testNonImagesAreRejectedAndNothingIsStored(): void
    {
        $albumId = $this->album();
        $path    = tempnam(sys_get_temp_dir(), 'gal');
        file_put_contents($path, '<?php echo "not a photo";');
        $this->tempFiles[] = $path;

        $this->expectException(\InvalidArgumentException::class);

        try {
            (new GalleryService())->addPhoto($albumId, $path, hash('sha256', 'x'), 'evil.jpg', 1, null);
        } finally {
            $this->assertSame([], $this->r2->objects);
            $this->assertSame(0, (new GalleryPhotoModel())->countAllResults());
        }
    }

    public function testStorageFailureLeavesNoRecord(): void
    {
        $albumId           = $this->album();
        $this->r2->failPuts = true;

        try {
            $this->addPhoto($albumId, 1);
            $this->fail('Expected the storage failure to surface.');
        } catch (\RuntimeException) {
        }

        $this->assertSame(0, (new GalleryPhotoModel())->countAllResults());
    }

    public function testDeletingPhotosRemovesTheirFilesAndMovesTheCover(): void
    {
        $albumId = $this->album();
        $first   = $this->addPhoto($albumId, 1);
        $second  = $this->addPhoto($albumId, 2);

        $this->withSession(self::ADMIN)
            ->post("admin/gallery/album/{$albumId}/photos/delete", ['ids' => [$first['id']]])
            ->assertRedirect();

        $this->assertCount(2, $this->r2->objects);
        $this->assertArrayNotHasKey($first['storage_key'] . '-web.webp', $this->r2->objects);

        $album = (new GalleryAlbumModel())->find($albumId);
        $this->assertSame(1, (int) $album['photo_count']);
        $this->assertSame((int) $second['id'], (int) $album['cover_photo_id']);
    }

    public function testDeletingAnAlbumEmptiesItsStorage(): void
    {
        $albumId = $this->album();
        $this->addPhoto($albumId, 1);
        $this->addPhoto($albumId, 2);

        $this->withSession(self::ADMIN)->post("admin/gallery/delete/{$albumId}")->assertRedirect();

        $this->assertSame([], $this->r2->objects);
        $this->assertNull((new GalleryAlbumModel())->find($albumId));
        $this->assertSame(0, (new GalleryPhotoModel())->countAllResults());
    }

    public function testReorderOnlyTouchesThisAlbumsPhotos(): void
    {
        $albumId = $this->album();
        $other   = $this->album(['slug' => 'other']);
        $a       = $this->addPhoto($albumId, 1);
        $b       = $this->addPhoto($albumId, 2);
        $foreign = $this->addPhoto($other, 3);

        $this->withSession(self::ADMIN)
            ->post("admin/gallery/album/{$albumId}/reorder", ['order' => "{$b['id']},{$a['id']},{$foreign['id']}"])
            ->assertOK();

        $ids = array_column((new GalleryPhotoModel())->forAlbum($albumId)->findAll(), 'id');
        $this->assertSame([(int) $b['id'], (int) $a['id']], array_map('intval', $ids));
        $this->assertSame(3, (int) (new GalleryPhotoModel())->find($foreign['id'])['sort']);
    }

    // -----------------------------------------------------------------
    // Public pages
    // -----------------------------------------------------------------

    public function testOnlyPublishedAlbumsWithPhotosArePublic(): void
    {
        $live  = $this->album(['title' => 'Live Album', 'slug' => 'live']);
        $draft = $this->album(['title' => 'Draft Album', 'slug' => 'draft', 'is_published' => 0]);
        $this->album(['title' => 'Empty Album', 'slug' => 'empty']);
        $this->addPhoto($live, 1);
        $this->addPhoto($draft, 2);

        $index = $this->get('gallery');
        $index->assertOK();
        $index->assertSee('Live Album');
        $index->assertDontSee('Draft Album');
        $index->assertDontSee('Empty Album');

        $this->get('gallery/live')->assertOK();

        foreach (['draft', 'empty'] as $slug) {
            try {
                $this->get("gallery/{$slug}");
                $this->fail("/gallery/{$slug} should not be public.");
            } catch (\CodeIgniter\Exceptions\PageNotFoundException) {
            }
        }

        // Admins can preview a draft.
        $preview = $this->withSession(self::ADMIN)->get('gallery/draft');
        $preview->assertOK();
        $preview->assertSee('Preview.');
    }

    public function testAlbumPagesLoadMorePhotosInBatches(): void
    {
        $config          = config('Gallery');
        $config->perPage = 2;
        Factories::injectMock('config', 'Gallery', $config);

        $albumId = $this->album();
        foreach ([1, 2, 3] as $n) {
            $this->addPhoto($albumId, $n);
        }

        $page = $this->get('gallery/navratri-2026');
        $page->assertOK();
        $this->assertSame(2, substr_count($page->response()->getBody(), 'class="gallery-item"'));
        $page->assertSee('Load more photos');
        $page->assertSee('Photos by NiteshTakePhotos for LCNL');
        $page->assertSee('Contact us');

        $more = $this->get('gallery/navratri-2026?page=2&partial=1');
        $more->assertOK();
        $this->assertSame(1, substr_count($more->response()->getBody(), 'class="gallery-item"'));
        $this->assertStringNotContainsString('<html', $more->response()->getBody());
        $this->assertStringContainsString('data-next-page=""', $more->response()->getBody());
    }

    public function testDownloadIsAJpegAttachment(): void
    {
        $albumId = $this->album();
        $photo   = $this->addPhoto($albumId, 1);

        $result = $this->get("gallery/photo/{$photo['id']}/download");
        $result->assertOK();
        $result->assertHeader('Content-Type', 'image/jpeg');
        $this->assertStringContainsString('attachment; filename="lcnl-navratri-2026-', $result->response()->getHeaderLine('Content-Disposition'));
        $this->assertSame(IMAGETYPE_JPEG, getimagesizefromstring($result->response()->getBody())[2]);
    }

    public function testEventPageLinksToItsAlbum(): void
    {
        $this->db->table('events')->insert([
            'title' => 'Navratri Night 3', 'event_date' => '2026-10-03', 'is_valid' => 1,
        ]);
        $eventId = $this->db->insertID();

        $this->get("events/{$eventId}")->assertDontSee('View photos');

        $albumId = $this->album(['event_id' => $eventId]);
        $this->addPhoto($albumId, 1);

        $result = $this->get("events/{$eventId}");
        $result->assertSee('View photos (1)');
        $result->assertSee('gallery/navratri-2026');
    }

    public function testHomePageShowsTheFourNewestPublicAlbums(): void
    {
        $this->assertStringNotContainsString('Latest Photos', $this->get('/')->response()->getBody());

        foreach (['2026-01-01', '2026-03-01', '2026-05-01', '2026-07-01', '2026-09-01'] as $i => $date) {
            $id = $this->album(['title' => "Album {$date}", 'slug' => "album-{$i}", 'album_date' => $date]);
            $this->addPhoto($id, $i + 1);
        }
        $draft = $this->album(['title' => 'Draft Album', 'slug' => 'draft', 'album_date' => '2026-12-01', 'is_published' => 0]);
        $this->addPhoto($draft, 9);

        $body = $this->get('/')->response()->getBody();
        $this->assertStringContainsString('Latest Photos', $body);
        $this->assertStringNotContainsString('Draft Album', $body);
        $this->assertStringNotContainsString('Album 2026-01-01', $body, 'Only the newest four.');

        $positions = array_map(static fn(string $d): int|false => strpos($body, "Album {$d}"), ['2026-09-01', '2026-07-01', '2026-05-01', '2026-03-01']);
        $this->assertNotContains(false, $positions);
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($sorted, $positions, 'Newest first.');
    }

    public function testSitemapListsPublicAlbumsOnly(): void
    {
        $live = $this->album(['slug' => 'live']);
        $this->addPhoto($live, 1);
        $this->album(['slug' => 'secret-draft', 'is_published' => 0]);

        $xml = $this->get('sitemap.xml')->getBody();
        $this->assertStringContainsString('gallery/live', $xml);
        $this->assertStringNotContainsString('secret-draft', $xml);
    }
}
