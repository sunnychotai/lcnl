<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GolfRegistrationModel;

class GolfRegistrationController extends BaseController
{
    public function index()
    {
        $model = new GolfRegistrationModel();
        $registrations = $model->orderBy('created_at', 'DESC')->findAll();

        return view('admin/golf/index', [
            'title'         => 'Golf Event 2026 – Registrations',
            'registrations' => $registrations,
        ]);
    }

    public function export()
    {
        $db = \Config\Database::connect();
        $b  = $db->table('golf_registrations');
        $b->select('
            registration_ref,
            p1_first_name, p1_last_name, p1_email, p1_phone, p1_handicap, p1_meal,
            p2_first_name, p2_last_name, p2_email, p2_phone, p2_handicap, p2_meal,
            p3_first_name, p3_last_name, p3_email, p3_phone, p3_handicap, p3_meal,
            status, created_at
        ');
        $b->orderBy('created_at', 'DESC');

        $filename = 'golf_registrations_' . date('Ymd') . '.csv';

        $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');
        $this->response->sendHeaders();

        $sql   = $b->getCompiledSelect();
        $query = $db->query($sql, [], false);

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

        $first = $query->getUnbufferedRow('array');
        if ($first) {
            fputcsv($out, array_keys($first));
            fputcsv($out, $first);
            while ($row = $query->getUnbufferedRow('array')) {
                fputcsv($out, $row);
            }
        }

        fclose($out);
        $query->freeResult();
        exit(0);
    }
}
