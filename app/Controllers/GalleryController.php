<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\GalleryAlbumModel;
use App\Models\GalleryPhotoModel;
use App\Services\GalleryService;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Gallery;
use Throwable;

/**
 * Public photo gallery. Albums appear once they are published and hold at
 * least one photo; signed-in admins can preview the rest.
 */
class GalleryController extends BaseController
{
    protected Gallery $config;
    protected GalleryAlbumModel $albums;
    protected GalleryPhotoModel $photos;
    protected GalleryService $gallery;

    public function __construct()
    {
        $this->config  = config('Gallery');
        $this->albums  = new GalleryAlbumModel();
        $this->photos  = new GalleryPhotoModel();
        $this->gallery = new GalleryService();
    }

    public function index()
    {
        $albums = $this->albums->visible()
            ->orderBy('album_date IS NULL', '', false)
            ->orderBy('album_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($this->config->albumsPerPage);

        $covers = [];
        $ids    = array_filter(array_column($albums, 'cover_photo_id'));
        if ($ids !== []) {
            foreach ($this->photos->whereIn('id', $ids)->findAll() as $photo) {
                $covers[(int) $photo['album_id']] = $photo + ['urls' => $this->gallery->urls($photo)];
            }
        }

        return view('gallery/index', [
            'title'           => 'Photo Gallery',
            'metaDescription' => 'Photographs from LCNL events, festivals and community gatherings.',
            'albums'          => $albums,
            'covers'          => $covers,
            'pager'           => $this->albums->pager,
        ]);
    }

    /**
     * One album, a page of photos at a time. "Load more" asks for the next
     * page with ?partial=1 and appends the fragment; without JavaScript the
     * same link opens that page on its own.
     */
    public function album(string $slug)
    {
        $album = $this->findAlbum($slug);
        $page  = max(1, (int) $this->request->getGet('page'));
        $per   = $this->config->perPage;

        $photos = $this->photos->forAlbum((int) $album['id'])->findAll($per, ($page - 1) * $per);
        $photos = array_map(fn(array $p): array => $p + ['urls' => $this->gallery->urls($p)], $photos);

        $total    = $this->photos->where('album_id', $album['id'])->countAllResults();
        $nextPage = $page * $per < $total ? $page + 1 : null;

        if ($this->request->getGet('partial')) {
            return view('gallery/_photos', [
                'album'    => $album,
                'photos'   => $photos,
                'offset'   => ($page - 1) * $per,
                'nextPage' => $nextPage,
            ], ['debug' => false]);
        }

        $event = null;
        if (! empty($album['event_id'])) {
            $event = (new EventModel())->where('id', $album['event_id'])->where('is_valid', 1)->first();
        }

        $cover = $album['cover_photo_id'] ? $this->photos->find($album['cover_photo_id']) : null;

        return view('gallery/album', [
            'title'           => $album['title'],
            'metaDescription' => $this->shareDescription($album, $total),
            'ogImage'         => $cover ? $this->gallery->urls($cover)['web'] : null,
            'ogImageAlt'      => $cover ? 'Photo from ' . $album['title'] : null,
            'noindex'         => ! $this->isVisible($album),
            'album'           => $album,
            'photos'          => $photos,
            'total'           => $total,
            'page'            => $page,
            'offset'          => ($page - 1) * $per,
            'nextPage'        => $nextPage,
            'event'           => $event,
            'isPreview'       => ! $this->isVisible($album),
        ]);
    }

    /**
     * The web-size image as a JPEG attachment. Served through here because a
     * browser ignores the download attribute on another domain's link, and
     * JPEG opens everywhere, unlike WebP.
     */
    public function download(int $id)
    {
        $photo = $this->photos->find($id);
        $album = $photo ? $this->albums->find($photo['album_id']) : null;

        if (! $photo || ! $album || ! ($this->isVisible($album) || $this->isAdmin())) {
            throw PageNotFoundException::forPageNotFound();
        }

        try {
            $image = imagecreatefromstring($this->gallery->fetchWeb($photo));
        } catch (Throwable $e) {
            log_message('error', 'Gallery: download of photo {id} failed: {msg}', ['id' => $id, 'msg' => $e->getMessage()]);
            $image = false;
        }

        if ($image === false) {
            return $this->response->setStatusCode(503)->setBody('This photo is unavailable right now. Please try again shortly.');
        }

        ob_start();
        imagejpeg($image, null, 90);
        $jpeg = (string) ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'image/jpeg')
            ->setHeader('Content-Disposition', 'attachment; filename="lcnl-' . $album['slug'] . '-' . $id . '.jpg"')
            ->setHeader('Cache-Control', 'private, max-age=86400')
            ->setBody($jpeg);
    }

    private function findAlbum(string $slug): array
    {
        $album = $this->albums->findVisibleBySlug($slug);

        if (! $album && $this->isAdmin()) {
            $album = $this->albums->where('slug', $slug)->first();
        }

        if (! $album) {
            throw PageNotFoundException::forPageNotFound('Album not found');
        }

        return $album;
    }

    private function isVisible(array $album): bool
    {
        return ! empty($album['is_published']) && (int) $album['photo_count'] > 0;
    }

    private function isAdmin(): bool
    {
        return (bool) session('isAdminLoggedIn');
    }

    private function shareDescription(array $album, int $total): string
    {
        if (! empty($album['description'])) {
            return mb_strimwidth(trim(preg_replace('/\s+/u', ' ', strip_tags($album['description']))), 0, 160, '…');
        }

        $when = $album['album_date'] ? ' from ' . date('j F Y', strtotime($album['album_date'])) : '';

        return "{$total} photographs{$when} — " . $album['title'] . ', Lohana Community North London.';
    }
}
