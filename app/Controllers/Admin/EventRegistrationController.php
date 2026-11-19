<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\EventRegistrationModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class EventRegistrationController extends BaseController
{
    /** Statuses an admin can move a registration to, with the flash wording for each. */
    private const STATUS_ACTIONS = [
        'confirmed' => 'confirmed',
        'cancelled' => 'cancelled',
        'submitted' => 'reinstated',
    ];

    public function index(): string
    {
        return view('admin/event_registrations/index');
    }

    public function summary(): ResponseInterface
    {
        $db = db_connect();

        // Registrations and headcount count only live (non-cancelled) bookings,
        // matching how EventRegistrationModel works out capacity.
        $rows = $db->query("
            SELECT
              e.id AS event_id,
              e.title AS event_name,
              e.event_date,
              SUM(CASE WHEN r.status <> 'cancelled' THEN 1 ELSE 0 END) AS registrations,
              COALESCE(SUM(CASE WHEN r.status <> 'cancelled' THEN r.num_participants + r.num_guests ELSE 0 END), 0) AS total_people,
              COALESCE(SUM(CASE WHEN r.status <> 'cancelled' THEN r.num_guests ELSE 0 END), 0) AS total_guests,
              SUM(CASE WHEN r.status <> 'cancelled' AND r.member_id IS NOT NULL THEN 1 ELSE 0 END) AS member_registrations,
              SUM(CASE WHEN r.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
              SUM(CASE WHEN r.status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
              SUM(CASE WHEN r.status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
              MAX(r.created_at) AS last_registered_at
            FROM event_registrations r
            JOIN events e ON e.id = r.event_id
            GROUP BY r.event_id, e.title, e.event_date
            ORDER BY MAX(r.created_at) DESC
        ")->getResultArray();

        // DataTables expects { data: [...] }
        return $this->response->setJSON(['data' => $rows]);
    }

    /**
     * Every registration for one event.
     */
    public function event(int $eventId)
    {
        $event = (new EventModel())->find($eventId);

        if (!$event) {
            return redirect()->to(site_url('admin/content/events/event-registrations'))
                ->with('error', 'Event not found.');
        }

        $registrations = (new EventRegistrationModel())
            ->where('event_id', $eventId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $stats = ['live' => 0, 'people' => 0, 'submitted' => 0, 'confirmed' => 0, 'cancelled' => 0];
        foreach ($registrations as $r) {
            $stats[$r['status']] = ($stats[$r['status']] ?? 0) + 1;
            if ($r['status'] !== 'cancelled') {
                $stats['live']++;
                $stats['people'] += (int) $r['num_participants'] + (int) $r['num_guests'];
            }
        }

        return view('admin/event_registrations/event', [
            'event'         => $event,
            'registrations' => $registrations,
            'stats'         => $stats,
        ]);
    }

    /**
     * CSV of every registration (including cancelled) for one event.
     */
    public function export(int $eventId)
    {
        $event = (new EventModel())->find($eventId);

        if (!$event) {
            return redirect()->to(site_url('admin/content/events/event-registrations'))
                ->with('error', 'Event not found.');
        }

        $rows = db_connect()->table('event_registrations')
            ->select("
                id AS registration_id,
                created_at AS registered_at,
                first_name, last_name, email, phone,
                num_participants, num_guests,
                (num_participants + num_guests) AS party_size,
                CASE WHEN member_id IS NULL THEN 'No' ELSE 'Yes' END AS lcnl_member,
                member_id,
                notes, status
            ")
            ->where('event_id', $eventId)
            ->orderBy('created_at', 'ASC')
            ->get()
            ->getResultArray();

        $slug     = $event['slug'] ?: url_title($event['title'], '-', true);
        $filename = 'registrations_' . $slug . '_' . date('Ymd') . '.csv';

        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

        fputcsv($out, $rows ? array_keys($rows[0]) : [
            'registration_id', 'registered_at', 'first_name', 'last_name', 'email', 'phone',
            'num_participants', 'num_guests', 'party_size', 'lcnl_member', 'member_id', 'notes', 'status',
        ]);
        foreach ($rows as $row) {
            fputcsv($out, array_map([$this, 'csvSafe'], $row));
        }

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->setBody($csv);
    }

    /**
     * Confirm, cancel or reinstate a registration. Cancelled bookings no
     * longer count towards the event's capacity.
     */
    public function setStatus(int $id): RedirectResponse
    {
        $status = (string) $this->request->getPost('status');
        $model  = new EventRegistrationModel();
        $reg    = $model->find($id);

        if (!$reg) {
            return redirect()->to(site_url('admin/content/events/event-registrations'))
                ->with('error', 'Registration not found.');
        }

        $back = redirect()->to(site_url('admin/content/events/event-registrations/' . $reg['event_id']));
        $name = trim($reg['first_name'] . ' ' . $reg['last_name']);

        if (!isset(self::STATUS_ACTIONS[$status])) {
            return $back->with('error', 'Unknown status.');
        }

        if ($reg['status'] === $status) {
            return $back->with('error', $name . '\'s registration is already ' . $status . '.');
        }

        $model->update($id, ['status' => $status]);

        return $back->with('success', $name . '\'s registration has been ' . self::STATUS_ACTIONS[$status] . '.');
    }

    /** Stop spreadsheet apps treating user-entered text as a formula. */
    private function csvSafe($value)
    {
        if (
            is_string($value) && $value !== ''
            && strpbrk($value[0], '=+-@') !== false
            && !preg_match('/^\+?[\d\s()-]+$/', $value) // leave phone numbers alone
        ) {
            return "'" . $value;
        }

        return $value;
    }
}
