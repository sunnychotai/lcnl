<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;

class Events extends BaseController
{
    protected $eventModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'events' => $this->eventModel->orderBy('event_date', 'DESC')->findAll()
        ];
        return view('admin/content/events/index', $data);
    }

    public function create()
    {
        $data = [
            'event'  => [],
            'action' => base_url('admin/content/events/store')
        ];
        return view('admin/content/events/form', $data);
    }

    public function store()
    {
        $validationRules = [
            'title'       => 'required|min_length[3]',
            'event_date'  => 'required|valid_date',
            // New free-text fields (optional)
            'ticketinfo'  => 'permit_empty',
            'eventterms'  => 'permit_empty',
            'contactinfo' => 'permit_empty',
            // Image optional-if-present, must be valid when provided
            'image'       => 'if_exist|uploaded[image]|is_image[image]|max_size[image,2048]|mime_in[image,image/jpg,image/jpeg,image/png,image/gif,image/webp]',
        ];

        if (! $this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Whitelist posted fields explicitly (safer than grabbing everything)
        $data = $this->request->getPost([
            'title', 'event_date', 'time_from', 'time_to', 'committee', 'location', 'description',
            'ticketinfo', 'eventterms', 'contactinfo'
        ]);

        // Handle file upload
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $newName = $file->getRandomName();

            // Ensure target directory exists
            $targetDir = rtrim((string) getenv('UPLOAD_PATH'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'events';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $file->move($targetDir, $newName);
            $data['image'] = '/uploads/events/' . $newName; // public URL you use elsewhere
        }

        $this->eventModel->save($data);
        return redirect()->to('/admin/content/events')->with('success', 'Event added successfully');
    }

    public function edit($id)
    {
        $event = $this->eventModel->find($id);
        if (! $event) {
            return redirect()->to('/admin/content/events')->with('error', 'Event not found');
        }

        $data = [
            'event'  => $event,
            'action' => base_url('admin/content/events/update/' . $id)
        ];
        return view('admin/content/events/form', $data);
    }

    public function update($id)
    {
        $validationRules = [
            'title'       => 'required|min_length[3]',
            'event_date'  => 'required|valid_date',
            // New free-text fields (optional)
            'ticketinfo'  => 'permit_empty',
            'eventterms'  => 'permit_empty',
            'contactinfo' => 'permit_empty',
            // Image optional; validated only if present
            'image'       => 'if_exist|is_image[image]|max_size[image,2048]|mime_in[image,image/jpg,image/jpeg,image/png,image/gif,image/webp]',
        ];

        if (! $this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Whitelist posted fields
        $data = $this->request->getPost([
            'title', 'event_date', 'time_from', 'time_to', 'committee', 'location', 'description',
            'ticketinfo', 'eventterms', 'contactinfo'
        ]);

        // Handle new file upload (replace old if uploaded)
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $newName = $file->getRandomName();

            $targetDir = rtrim((string) getenv('UPLOAD_PATH'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'events';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $file->move($targetDir, $newName);
            $data['image'] = '/uploads/events/' . $newName;
        }

        $this->eventModel->update($id, $data);
        return redirect()->to('/admin/content/events')->with('success', 'Event updated successfully');
    }

    public function delete($id)
    {
        $this->eventModel->delete($id);
        return redirect()->to('/admin/content/events')->with('success', 'Event deleted');
    }

    public function clone($id)
    {
        $event = $this->eventModel->find($id);
        if (! $event) {
            return redirect()->to('/admin/content/events')->with('error', 'Event not found');
        }

        unset($event['id']); // keeps all other fields, including the new ones
        $this->eventModel->insert($event);

        return redirect()->to('/admin/content/events')->with('success', 'Event cloned successfully');
    }
}
