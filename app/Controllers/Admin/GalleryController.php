<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\GalleryAlbumModel;
use App\Models\GalleryPhotoModel;
use App\Services\GalleryService;
use Config\Gallery;
use Throwable;

/**
 * Photo gallery back-office. ADMIN only.
 *
 * Uploads arrive one photo per request from the browser uploader, already
 * resized, so no request comes near PHP's upload or time limits however many
 * photos are in the batch.
 */
class GalleryController extends BaseController
{
    private const BASE = 'admin/gallery';

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
        $albums = $this->albums
            ->orderBy('album_date IS NULL', '', false)
            ->orderBy('album_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('admin/gallery/index', [
            'title'        => 'Photo Gallery',
            'albums'       => $albums,
            'covers'       => $this->coverUrls($albums),
            'storageReady' => $this->gallery->storageReady(),
        ]);
    }

    public function create()
    {
        return view('admin/gallery/form', [
            'title'  => 'New Album',
            'album'  => ['credit' => $this->config->defaultCredit],
            'events' => $this->eventOptions(),
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data               = $this->formData();
        $data['slug']       = $this->albums->uniqueSlug($this->request->getPost('slug') ?: $data['title']);
        $data['created_by'] = session('admin_name');

        $id = $this->albums->insert($data, true);

        return redirect()->to(self::BASE . '/album/' . $id)
            ->with('success', 'Album created. Now add some photos.');
    }

    public function edit(int $id)
    {
        $album = $this->albums->find($id);

        if (! $album) {
            return redirect()->to(self::BASE)->with('error', 'Album not found.');
        }

        return view('admin/gallery/form', [
            'title'  => 'Edit Album',
            'album'  => $album,
            'events' => $this->eventOptions(),
        ]);
    }

    public function update(int $id)
    {
        $album = $this->albums->find($id);

        if (! $album) {
            return redirect()->to(self::BASE)->with('error', 'Album not found.');
        }

        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data         = $this->formData();
        $data['slug'] = $this->albums->uniqueSlug($this->request->getPost('slug') ?: $data['title'], $id);

        $this->albums->update($id, $data);

        return redirect()->to(self::BASE . '/album/' . $id)->with('success', 'Album details saved.');
    }

    public function delete(int $id)
    {
        $album = $this->albums->find($id);

        if (! $album) {
            return redirect()->to(self::BASE)->with('error', 'Album not found.');
        }

        try {
            $this->gallery->deleteAlbum($id);
        } catch (Throwable $e) {
            log_message('error', 'Gallery: deleting album {id} failed: {msg}', ['id' => $id, 'msg' => $e->getMessage()]);

            return redirect()->to(self::BASE . '/album/' . $id)
                ->with('error', 'The photos could not be removed from storage, so the album was kept. Please try again.');
        }

        return redirect()->to(self::BASE)->with('success', 'Album “' . $album['title'] . '” and its photos were deleted.');
    }

    public function togglePublish(int $id)
    {
        $album = $this->albums->find($id);

        if (! $album) {
            return redirect()->to(self::BASE)->with('error', 'Album not found.');
        }

        $publish = empty($album['is_published']);
        $this->albums->update($id, ['is_published' => $publish ? 1 : 0]);

        $message = $publish
            ? ((int) $album['photo_count'] > 0
                ? 'Album published — it is now on the public gallery.'
                : 'Album published. It will appear on the public gallery once it has photos.')
            : 'Album unpublished — it is no longer listed publicly.';

        return redirect()->to(self::BASE . '/album/' . $id)->with('success', $message);
    }

    /** Photo management and the uploader. */
    public function album(int $id)
    {
        $album = $this->albums->find($id);

        if (! $album) {
            return redirect()->to(self::BASE)->with('error', 'Album not found.');
        }

        $photos = $this->photos->forAlbum($id)->findAll();

        return view('admin/gallery/album', [
            'title'        => $album['title'] . ' – Photos',
            'album'        => $album,
            'photos'       => array_map(fn(array $p): array => $p + ['thumb' => $this->gallery->urls($p)['thumb']], $photos),
            'nextSort'     => $this->gallery->nextSort($id),
            'storageReady' => $this->gallery->storageReady(),
            'maxBytes'     => $this->config->maxUploadBytes,
            'webSize'      => $this->config->variants['web']['width'],
        ]);
    }

    /** Which of the posted hashes are already in the album (JSON). */
    public function check(int $id)
    {
        if (! $this->albums->find($id)) {
            return $this->json(['error' => 'Album not found.'], 404);
        }

        $hashes = (array) ($this->request->getPost('hashes') ?? []);

        return $this->json(['existing' => $this->gallery->existingHashes($id, array_slice($hashes, 0, 2000))]);
    }

    /** One photo per request (JSON). */
    public function upload(int $id)
    {
        if (! $this->albums->find($id)) {
            return $this->json(['error' => 'Album not found.'], 404);
        }

        $file = $this->request->getFile('photo');

        if (! $file || ! $file->isValid()) {
            return $this->json(['error' => $file ? $file->getErrorString() : 'No file was received.'], 422);
        }

        if ($file->getSize() > $this->config->maxUploadBytes) {
            return $this->json(['error' => 'The file is too large.'], 422);
        }

        try {
            $result = $this->gallery->addPhoto(
                $id,
                $file->getTempName(),
                (string) $this->request->getPost('hash'),
                $this->request->getPost('name') ?: $file->getClientName(),
                (int) $this->request->getPost('sort'),
                session('admin_name'),
            );
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            log_message('error', 'Gallery: upload to album {id} failed: {msg}', ['id' => $id, 'msg' => $e->getMessage()]);

            // 503 tells the uploader this is worth retrying.
            return $this->json(['error' => 'Storage is not responding. Retrying…'], 503);
        }

        if ($result['status'] === 'duplicate') {
            return $this->json(['status' => 'duplicate']);
        }

        $photo = $result['photo'];

        return $this->json([
            'status' => 'added',
            'photo'  => [
                'id'    => (int) $photo['id'],
                'name'  => $photo['original_name'],
                'thumb' => $this->gallery->urls($photo)['thumb'],
            ],
        ]);
    }

    public function setCover(int $id, int $photoId)
    {
        $ok = $this->gallery->setCover($id, $photoId);

        return redirect()->to(self::BASE . '/album/' . $id)
            ->with($ok ? 'success' : 'error', $ok ? 'Cover photo updated.' : 'Photo not found in this album.');
    }

    public function deletePhotos(int $id)
    {
        $ids = (array) ($this->request->getPost('ids') ?? []);

        try {
            $deleted = $this->gallery->deletePhotos($id, $ids);
        } catch (Throwable $e) {
            log_message('error', 'Gallery: deleting photos from album {id} failed: {msg}', ['id' => $id, 'msg' => $e->getMessage()]);

            return redirect()->to(self::BASE . '/album/' . $id)
                ->with('error', 'The photos could not be removed from storage. Nothing was deleted — please try again.');
        }

        return redirect()->to(self::BASE . '/album/' . $id)
            ->with('success', $deleted === 1 ? '1 photo deleted.' : "{$deleted} photos deleted.");
    }

    /** Save drag-and-drop order (JSON). */
    public function reorder(int $id)
    {
        if (! $this->albums->find($id)) {
            return $this->json(['error' => 'Album not found.'], 404);
        }

        // Comma-separated: thousands of order[] fields would exceed PHP's
        // max_input_vars and be silently truncated.
        $order = array_filter(explode(',', (string) $this->request->getPost('order')), 'ctype_digit');

        $this->gallery->reorder($id, array_map('intval', $order));

        return $this->json(['status' => 'ok']);
    }

    /**
     * A current CSRF token. The token lasts two hours, and a large upload can
     * outlast it, so the uploader asks for a fresh one as it goes.
     */
    public function token()
    {
        return $this->json(['token' => csrf_hash()]);
    }

    private function rules(): array
    {
        return [
            'title'       => 'required|trim|max_length[150]',
            'slug'        => 'permit_empty|trim|max_length[150]',
            'album_date'  => 'permit_empty|valid_date[Y-m-d]',
            'event_id'    => 'permit_empty|is_natural_no_zero',
            'description' => 'permit_empty|max_length[5000]',
            'credit'      => 'permit_empty|trim|max_length[150]',
        ];
    }

    private function formData(): array
    {
        $post = $this->request->getPost();

        return [
            'title'       => trim($post['title']),
            'album_date'  => ($post['album_date'] ?? '') ?: null,
            'event_id'    => ($post['event_id'] ?? '') ?: null,
            'description' => trim($post['description'] ?? '') ?: null,
            'credit'      => trim($post['credit'] ?? '') ?: null,
        ];
    }

    /** Most recent first; an album can be linked to any event, past or future. */
    private function eventOptions(): array
    {
        return (new EventModel())
            ->select('id, title, event_date')
            ->orderBy('event_date', 'DESC')
            ->findAll(300);
    }

    /** @return array<int, string> album id => cover thumbnail URL */
    private function coverUrls(array $albums): array
    {
        $ids = array_filter(array_column($albums, 'cover_photo_id'));

        if ($ids === []) {
            return [];
        }

        $urls = [];
        foreach ($this->photos->whereIn('id', $ids)->findAll() as $photo) {
            $urls[(int) $photo['album_id']] = $this->gallery->urls($photo)['thumb'];
        }

        return $urls;
    }

    private function json(array $body, int $status = 200)
    {
        return $this->response->setStatusCode($status)->setJSON($body + ['csrf' => csrf_hash()]);
    }
}
