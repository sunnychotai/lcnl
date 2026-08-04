<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MelaStallBookingModel;
use App\Models\MelaStallDocumentModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\MelaStalls;

/**
 * Back-office for Mela stall bookings.
 *
 * Uploaded documents live outside the web root and are streamed from here, so
 * they are only ever readable by a signed-in admin.
 */
class MelaStallController extends BaseController
{
    protected MelaStalls $config;
    protected MelaStallBookingModel $bookings;
    protected MelaStallDocumentModel $documents;

    public function __construct()
    {
        $this->config    = config('MelaStalls');
        $this->bookings  = new MelaStallBookingModel();
        $this->documents = new MelaStallDocumentModel();
    }

    public function index()
    {
        $rows = $this->bookings->orderBy('created_at', 'DESC')->findAll();

        // Attach documents and flag likely duplicates for review.
        $seen = [];
        foreach ($rows as &$row) {
            $row['documents'] = $this->documents->forBooking((int) $row['id']);

            $key = strtolower($row['contact_email']) . '|' . strtolower(trim($row['company_name']));
            $row['is_duplicate'] = isset($seen[$key]);
            $seen[$key] = true;
        }
        unset($row);

        $active = array_filter($rows, static fn(array $r): bool => $r['status'] !== 'cancelled');

        return view('admin/content/mela/index', [
            'title'    => 'Mela Stall Bookings',
            'bookings' => $rows,
            'config'   => $this->config,
            'stats'    => [
                'total'    => count($active),
                'paid'     => count(array_filter($active, static fn(array $r): bool => (bool) $r['payment_received'])),
                'unpaid'   => count(array_filter($active, static fn(array $r): bool => ! $r['payment_received'])),
                'food'     => count(array_filter($active, static fn(array $r): bool => (bool) $r['is_food_stall'])),
                'foodNoDoc' => count(array_filter($active, static fn(array $r): bool =>
                    $r['is_food_stall'] && empty($r['documents']))),
            ],
        ]);
    }

    /** Toggle whether the transfer has been matched on the bank statement. */
    public function togglePayment(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking) {
            return redirect()->to('/admin/content/mela-stalls')->with('error', 'Booking not found.');
        }

        $nowPaid = empty($booking['payment_received']);

        $this->bookings->update($id, [
            'payment_received'    => $nowPaid ? 1 : 0,
            'payment_received_at' => $nowPaid ? date('Y-m-d H:i:s') : null,
            'payment_marked_by'   => $nowPaid ? (session()->get('admin_name') ?? 'Admin') : null,
            // Payment is what confirms a stall.
            'status'              => $nowPaid ? 'confirmed' : 'submitted',
        ]);

        return redirect()->to('/admin/content/mela-stalls')->with(
            'success',
            $booking['company_name'] . ($nowPaid ? ' marked as paid.' : ' marked as unpaid.')
        );
    }

    public function cancel(int $id)
    {
        $booking = $this->bookings->find($id);

        if (! $booking) {
            return redirect()->to('/admin/content/mela-stalls')->with('error', 'Booking not found.');
        }

        $this->bookings->update($id, ['status' => 'cancelled']);

        return redirect()->to('/admin/content/mela-stalls')
            ->with('success', $booking['company_name'] . ' cancelled.');
    }

    /**
     * Stream an uploaded document.
     *
     * The stored name is looked up in the database rather than taken from the
     * URL, so a crafted path cannot escape the upload directory.
     */
    public function document(int $id)
    {
        $doc = $this->documents->find($id);

        if (! $doc) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = $this->config->documentPath() . basename($doc['stored_name']);

        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', $doc['mime_type'] ?: 'application/octet-stream')
            // inline so certificates open in the browser; filename is the original
            ->setHeader('Content-Disposition',
                'inline; filename="' . str_replace('"', '', $doc['original_name']) . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Cache-Control', 'private, no-store')
            ->setBody(file_get_contents($path));
    }

    public function export()
    {
        $rows = $this->bookings->orderBy('created_at', 'DESC')->findAll();

        $filename = 'mela_stall_bookings_' . date('Ymd') . '.csv';

        $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $this->response->sendHeaders();

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads accents correctly

        fputcsv($out, [
            'Reference', 'Company', 'Stall type', 'Food stall', 'Items',
            'Contact name', 'Phone', 'Email', 'Vehicle',
            'Says paid', 'Payment received', 'Paid marked by', 'Paid at',
            'Status', 'Documents', 'Payment reference', 'Comments', 'Booked at',
        ]);

        foreach ($rows as $row) {
            $category = $this->config->categories[$row['category']] ?? $row['category'];

            if ($row['category'] === 'other' && ! empty($row['category_other'])) {
                $category .= ': ' . $row['category_other'];
            }

            fputcsv($out, [
                $row['booking_ref'],
                $row['company_name'],
                $category,
                $row['is_food_stall'] ? 'Yes' : 'No',
                $row['items_description'],
                $row['contact_name'],
                $row['contact_phone'],
                $row['contact_email'],
                $row['vehicle_reg'],
                $row['confirmed_payment'] ? 'Yes' : 'No',
                $row['payment_received'] ? 'Yes' : 'No',
                $row['payment_marked_by'],
                $row['payment_received_at'],
                $row['status'],
                count($this->documents->forBooking((int) $row['id'])),
                $this->config->paymentReference($row['company_name']),
                $row['comments'],
                $row['created_at'],
            ]);
        }

        fclose($out);
        exit;
    }
}
