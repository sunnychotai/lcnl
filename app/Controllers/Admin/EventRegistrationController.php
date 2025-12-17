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
              event_name,
              COUNT(*) AS registrations,
              COALESCE(SUM(num_participants), 0) AS total_people,
              COALESCE(SUM(num_guests), 0) AS total_guests,
              SUM(CASE WHEN member_id IS NOT NULL THEN 1 ELSE 0 END) AS member_registrations,
              SUM(CASE WHEN member_id IS NULL THEN 1 ELSE 0 END) AS guest_registrations,
              SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
              SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
              SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
              MIN(created_at) AS first_registered_at,
              MAX(created_at) AS last_registered_at
            FROM event_registrations
            GROUP BY event_name
            ORDER BY MAX(created_at) DESC
        ")->getResultArray();

        // DataTables expects { data: [...] }
        return $this->response->setJSON(['data' => $rows]);
    }
}
