<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GolfRegistrationModel;
use App\Models\EmailQueueModel;

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

    public function confirm(int $id)
    {
        $model = new GolfRegistrationModel();
        $reg   = $model->find($id);

        if (!$reg) {
            return redirect()->to(site_url('admin/content/golf'))
                ->with('error', 'Registration not found.');
        }

        if ($reg['status'] === 'confirmed') {
            return redirect()->to(site_url('admin/content/golf'))
                ->with('error', 'Registration ' . $reg['registration_ref'] . ' is already confirmed.');
        }

        $model->update($id, ['status' => 'confirmed']);

        // Build player list from the registration row
        $players = [];
        foreach (['p1', 'p2', 'p3', 'p4'] as $px) {
            if (!empty($reg[$px . '_first_name'])) {
                $players[] = [
                    'first_name' => $reg[$px . '_first_name'],
                    'full_name'  => $reg[$px . '_first_name'] . ' ' . $reg[$px . '_last_name'],
                    'email'      => $reg[$px . '_email'],
                    'handicap'   => $reg[$px . '_handicap'],
                    'meal'       => $reg[$px . '_meal'],
                    'tshirt'     => $reg[$px . '_tshirt'] ?? '',
                ];
            }
        }

        $emails = new EmailQueueModel();

        foreach ($players as $player) {
            $html = view('emails/golf_registration_confirmed', [
                'first_name'       => $player['first_name'],
                'registration_ref' => $reg['registration_ref'],
                'team_name'        => $reg['team_name'] ?? '',
                'all_players'      => $players,
            ]);

            $emails->enqueue([
                'to_email'  => $player['email'],
                'to_name'   => $player['full_name'],
                'subject'   => 'LCNL Golf Event 2026 – Registration Confirmed',
                'body_html' => $html,
                'body_text' => strip_tags($html),
                'priority'  => 1,
            ]);
        }

        $count = count($players);
        return redirect()->to(site_url('admin/content/golf'))
            ->with('success', 'Registration ' . $reg['registration_ref'] . ' marked as confirmed. ' . $count . ' confirmation email' . ($count !== 1 ? 's' : '') . ' queued.');
    }

    public function export()
    {
        $db = \Config\Database::connect();
        $b  = $db->table('golf_registrations');
        $b->select('
            registration_ref, team_name,
            p1_first_name, p1_last_name, p1_email, p1_phone, p1_handicap, p1_meal, p1_tshirt,
            p2_first_name, p2_last_name, p2_email, p2_phone, p2_handicap, p2_meal, p2_tshirt,
            p3_first_name, p3_last_name, p3_email, p3_phone, p3_handicap, p3_meal, p3_tshirt,
            p4_first_name, p4_last_name, p4_email, p4_phone, p4_handicap, p4_meal, p4_tshirt,
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
