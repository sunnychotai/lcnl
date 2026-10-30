<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class EventRegistrationController extends BaseController
{
    public function index(): string
    {
        return view('admin/event_registrations/index');
    }

    public function summary(): ResponseInterface
    {
        $db = db_connect();

        $rows = $db->query("
            SELECT
              e.title AS event_name,
              COUNT(*) AS registrations,
              COALESCE(SUM(r.num_participants), 0) AS total_people,
              COALESCE(SUM(r.num_guests), 0) AS total_guests,
              SUM(CASE WHEN r.member_id IS NOT NULL THEN 1 ELSE 0 END) AS member_registrations,
              SUM(CASE WHEN r.member_id IS NULL THEN 1 ELSE 0 END) AS guest_registrations,
              SUM(CASE WHEN r.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
              SUM(CASE WHEN r.status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
              SUM(CASE WHEN r.status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
              MIN(r.created_at) AS first_registered_at,
              MAX(r.created_at) AS last_registered_at
            FROM event_registrations r
            JOIN events e ON e.id = r.event_id
            GROUP BY r.event_id, e.title
            ORDER BY MAX(r.created_at) DESC
        ")->getResultArray();

        // DataTables expects { data: [...] }
        return $this->response->setJSON(['data' => $rows]);
    }
}
