<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\EventRegistrationModel;

class Events extends BaseController
{
    protected $eventModel;
    protected $registrationModel;

    protected $committeeOptions = [
        'LCNL' => 'LCNL',
        'YLS' => 'YLS',
        'Mahila' => 'Mahila',
        'Youth' => 'Youth',
    ];

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->registrationModel = new EventRegistrationModel();
        helper(['form', 'url', 'text']);
    }

    /* ======================================================
       INDEX
    ====================================================== */

    public function index()
    {
        $events = $this->eventModel
            ->orderBy('event_date', 'DESC')
            ->findAll();

        foreach ($events as &$event) {

            $event['current_registrations'] = 0;
            $event['current_headcount'] = 0;
            $event['is_full'] = false;

            if (!empty($event['requires_registration'])) {

                $eventId = (int) $event['id'];

                $registrations = (int) $this->registrationModel
                    ->getTotalRegistrationsForEventId($eventId);

                $headcount = (int) $this->registrationModel
                    ->getTotalHeadcountForEventId($eventId);

                $event['current_registrations'] = $registrations;
                $event['current_headcount'] = $headcount;

                $maxRegistrations = (int) ($event['max_registrations'] ?? 0);
                $maxHeadcount = (int) ($event['max_headcount'] ?? 0);

                // Check registration limit
                if ($maxRegistrations > 0 && $registrations >= $maxRegistrations) {
                    $event['is_full'] = true;
                }

                // Check headcount limit
                if ($maxHeadcount > 0 && $headcount >= $maxHeadcount) {
                    $event['is_full'] = true;
                }

                // Optional: calculate % for progress bars later
                $event['registration_percent'] = ($maxRegistrations > 0)
                    ? min(100, round(($registrations / $maxRegistrations) * 100))
                    : null;

                $event['headcount_percent'] = ($maxHeadcount > 0)
                    ? min(100, round(($headcount / $maxHeadcount) * 100))
                    : null;
            }
        }

        return view('admin/content/events/index', [
            'events' => $events
        ]);
    }

    /* ======================================================
       CREATE
    ====================================================== */

    public function create()
    {
        return view('admin/content/events/form', [
            'event' => [],
            'action' => base_url('admin/content/events/store'),
            'committeeOptions' => $this->committeeOptions,
        ]);
    }

    /* ======================================================
       STORE
    ====================================================== */

    public function store()
    {
        $rules = [
            'title' => 'required|min_length[3]',
            'event_date' => 'required|valid_date',
            'committee' => 'required|in_list[LCNL,YLS,Mahila,Youth]',
            'slug' => 'permit_empty|alpha_dash',
            'max_registrations' => 'permit_empty|integer|greater_than_equal_to[0]',
            'max_headcount' => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->getEventPostData();

        if (empty($data['slug'])) {
            $data['slug'] = url_title($data['title'], '-', true);
        }

        $data['slug'] = $this->makeSlugUnique($data['slug']);

        $this->handleImageUpload($data);

        $this->eventModel->insert($data);

        return redirect()->to('/admin/content/events')
            ->with('success', 'Event added successfully');
    }

    /* ======================================================
       UPDATE
    ====================================================== */

    public function update($id)
    {
        $rules = [
            'title' => 'required|min_length[3]',
            'event_date' => 'required|valid_date',
            'committee' => 'required|in_list[LCNL,YLS,Mahila,Youth]',
            'slug' => 'permit_empty|alpha_dash',
            'max_registrations' => 'permit_empty|integer|greater_than_equal_to[0]',
            'max_headcount' => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->getEventPostData();

        if (empty($data['slug'])) {
            $data['slug'] = url_title($data['title'], '-', true);
        }

        $data['slug'] = $this->makeSlugUnique($data['slug'], $id);

        $this->handleImageUpload($data);

        $this->eventModel->update($id, $data);

        return redirect()->to('/admin/content/events')
            ->with('success', 'Event updated successfully');
    }

    /* ======================================================
       DELETE
    ====================================================== */

    public function delete($id)
    {
        $this->eventModel->delete($id);

        return redirect()->to('/admin/content/events')
            ->with('success', 'Event deleted');
    }


    /* ======================================================
   EDIT
====================================================== */

    public function edit($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            return redirect()->to('/admin/content/events')
                ->with('error', 'Event not found');
        }

        return view('admin/content/events/form', [
            'event' => $event,
            'action' => base_url('admin/content/events/update/' . $id),
            'committeeOptions' => $this->committeeOptions,
        ]);
    }


    /* ======================================================
       CLONE
    ====================================================== */

    public function clone($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            return redirect()->to('/admin/content/events')
                ->with('error', 'Event not found');
        }

        unset($event['id']);

        $event['slug'] = $this->makeSlugUnique($event['slug'] . '-copy');

        $this->eventModel->insert($event);

        return redirect()->to('/admin/content/events')
            ->with('success', 'Event cloned successfully');
    }

    /* ======================================================
       HELPERS
    ====================================================== */

    private function getEventPostData(): array
    {
        return [
            'title' => $this->request->getPost('title'),
            'slug' => $this->request->getPost('slug'),
            'description' => $this->request->getPost('description'),
            'event_date' => $this->request->getPost('event_date'),
            'location' => $this->request->getPost('location'),
            'time_from' => $this->request->getPost('time_from'),
            'time_to' => $this->request->getPost('time_to'),
            'committee' => $this->request->getPost('committee'),
            'ticketinfo' => $this->request->getPost('ticketinfo'),
            'eventterms' => $this->request->getPost('eventterms'),
            'contactinfo' => $this->request->getPost('contactinfo'),
            'requires_registration' => (int) $this->request->getPost('requires_registration'),
            'max_registrations' => $this->request->getPost('max_registrations') ?: null,
            'max_headcount' => $this->request->getPost('max_headcount') ?: null,
            'is_valid' => (int) $this->request->getPost('is_valid'),
        ];
    }

    private function handleImageUpload(array &$data)
    {
        $file = $this->request->getFile('image');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return;
        }

        $newName = $file->getRandomName();

        // ✅ Always use FCPATH for public uploads
        $targetDir = FCPATH . 'uploads/events/';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (!$file->move($targetDir, $newName)) {
            throw new \RuntimeException($file->getErrorString());
        }

        $data['image'] = '/uploads/events/' . $newName;
    }


    private function makeSlugUnique(string $slug, int $ignoreId = null): string
    {
        $builder = $this->eventModel->where('slug', $slug);

        if ($ignoreId) {
            $builder->where('id !=', $ignoreId);
        }

        if (!$builder->first()) {
            return $slug;
        }

        return $slug . '-' . time();
    }
}
