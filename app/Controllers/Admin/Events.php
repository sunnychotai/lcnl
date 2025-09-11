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
        return view('admin/events/index', $data);
    }

    public function create()
    {
        $data = [
            'event'  => [],
            'action' => base_url('admin/events/store')
        ];
        return view('admin/events/form', $data);
    }

    public function store()
    {
        $validationRules = [
            'title'      => 'required|min_length[3]',
            'event_date' => 'required|valid_date',
            'image'      => 'if_exist|uploaded[image]|is_image[image]|max_size[image,2048]|mime_in[image,image/jpg,image/jpeg,image/png,image/gif,image/webp]'
        ];

        if (! $this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Handle file upload
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(getenv('UPLOAD_PATH') . 'events', $newName);
            $data['image'] = '/uploads/events/' . $newName;
        }

        $this->eventModel->save($data);
        return redirect()->to('/admin/events')->with('success', 'Event added successfully');
    }

    public function edit($id)
    {
        $event = $this->eventModel->find($id);
        if (! $event) {
            return redirect()->to('/admin/events')->with('error', 'Event not found');
        }

        $data = [
            'event'  => $event,
            'action' => base_url('admin/events/update/'.$id)
        ];
        return view('admin/events/form', $data);
    }

    public function update($id)
    {
        $validationRules = [
            'title'      => 'required|min_length[3]',
            'event_date' => 'required|valid_date',
            'image'      => 'if_exist|is_image[image]|max_size[image,2048]|mime_in[image,image/jpg,image/jpeg,image/png,image/gif,image/webp]'
        ];

        if (! $this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Handle new file upload (replace old if uploaded)
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(getenv('UPLOAD_PATH') . 'events', $newName);
            $data['image'] = '/uploads/events/' . $newName;
        }

        $this->eventModel->update($id, $data);
        return redirect()->to('/admin/events')->with('success', 'Event updated successfully');
    }

    public function delete($id)
    {
        $this->eventModel->delete($id);
        return redirect()->to('/admin/events')->with('success', 'Event deleted');
    }

    public function clone($id)
    {
        $event = $this->eventModel->find($id);
        if (! $event) {
            return redirect()->to('/admin/events')->with('error', 'Event not found');
        }

        unset($event['id']);
        $this->eventModel->insert($event);

        return redirect()->to('/admin/events')->with('success', 'Event cloned successfully');
    }
}
