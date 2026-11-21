<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\GroupEmail\Audience;
use App\Libraries\GroupEmail\GroupEmailException;
use App\Models\GroupEmailModel;
use App\Models\UserModel;
use App\Services\GroupEmailService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Write an email once and queue a personalised copy for everyone in an
 * audience (an event's registrants, for now). ADMIN only.
 *
 * Compose, preview, test and queue are addressed by audience key and
 * reference (e.g. /admin/group-emails/new/event/63), so a new audience
 * needs no new routes.
 */
class GroupEmailController extends BaseController
{
    private GroupEmailService $service;

    public function __construct()
    {
        $this->service = new GroupEmailService();
    }

    /** Every group email, newest first. */
    public function index(): string
    {
        $groupEmails = (new GroupEmailModel())->orderBy('id', 'DESC')->findAll(200);

        return view('admin/group_emails/index', [
            'groupEmails' => $groupEmails,
            'progress'    => $this->service->progress(array_column($groupEmails, 'id')),
        ]);
    }

    public function compose(string $key, int $ref)
    {
        $draft = null;
        $draftId = (int) ($this->request->getGet('draft') ?? old('draft_id') ?? 0);

        if ($draftId) {
            $found = (new GroupEmailModel())->find($draftId);
            if ($found && $found['status'] === 'draft' && $found['audience'] === $key && (int) $found['audience_ref'] === $ref) {
                $draft = $found;
            }
        }

        $filter   = $draft ? (json_decode((string) $draft['filter_json'], true) ?: []) : [];
        $audience = $this->service->audience($key, $ref, $filter);

        if (!$audience) {
            return redirect()->to(site_url('admin/group-emails'))->with('error', 'Those recipients can\'t be found.');
        }

        $previous = (new GroupEmailModel())
            ->where('audience', $key)
            ->where('audience_ref', $ref)
            ->where('status !=', 'draft')
            ->orderBy('id', 'DESC')
            ->findAll(10);

        return view('admin/group_emails/compose', [
            'key'          => $key,
            'audience'     => $audience,
            'draft'        => $draft,
            'testEmail'    => $this->adminEmail(),
            'replyTo'      => config('GroupEmail')->replyTo,
            'previous'     => $previous,
            'progress'     => $this->service->progress(array_column($previous, 'id')),
            'baseUrl'      => site_url('admin/group-emails/new/' . $key . '/' . $ref),
        ]);
    }

    public function preview(string $key, int $ref): ResponseInterface
    {
        $input    = $this->input();
        $audience = $this->service->audience($key, $ref, $input['filter']);

        if (!$audience) {
            return $this->fail('Those recipients can\'t be found.', 404);
        }

        return $this->response->setJSON(
            $this->service->preview($audience, $input['subject'], $input['body_html'], $input['wrap_layout'])
        );
    }

    public function test(string $key, int $ref): ResponseInterface
    {
        $input = $this->input();

        try {
            $id = $this->service->saveDraft($input['draft_id'], $this->audienceOrFail($key, $ref, $input), $key, $input, $this->admin());
            $this->service->sendTest($id, (string) $this->request->getPost('test_email'));
        } catch (GroupEmailException $e) {
            return $this->fail($e->getMessage());
        }

        return $this->response->setJSON([
            'ok'       => true,
            'draft_id' => $id,
            'message'  => 'Test queued for ' . trim((string) $this->request->getPost('test_email'))
                . '. It should arrive within a few minutes.',
        ]);
    }

    public function queue(string $key, int $ref): RedirectResponse
    {
        $input = $this->input();
        $id    = $input['draft_id'];

        try {
            $id    = $this->service->saveDraft($id, $this->audienceOrFail($key, $ref, $input), $key, $input, $this->admin());
            $count = $this->service->queue($id);
        } catch (GroupEmailException $e) {
            $back = $id
                ? redirect()->to(site_url('admin/group-emails/new/' . $key . '/' . $ref) . '?draft=' . $id)
                : redirect()->back()->withInput();

            return $back->with('error', $e->getMessage());
        }

        return redirect()->to(site_url('admin/group-emails/' . $id))
            ->with('success', $count . ' email' . ($count === 1 ? '' : 's') . ' queued. They go out as the queue works through them.');
    }

    public function show(int $id)
    {
        $groupEmail = (new GroupEmailModel())->find($id);

        if (!$groupEmail) {
            return redirect()->to(site_url('admin/group-emails'))->with('error', 'Group email not found.');
        }

        if ($groupEmail['status'] === 'draft') {
            return redirect()->to(site_url('admin/group-emails/new/' . $groupEmail['audience'] . '/' . $groupEmail['audience_ref']) . '?draft=' . $id);
        }

        $rows = db_connect()->table('email_queue')
            ->select('id, to_email, to_name, status, attempts, last_error, sent_at')
            ->where('group_email_id', $id)
            ->where('type', GroupEmailService::TYPE)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $audience = $this->service->audienceFor($groupEmail);

        return view('admin/group_emails/show', [
            'groupEmail' => $groupEmail,
            'progress'   => $this->service->progress([$id])[$id],
            'rows'       => $rows,
            'backUrl'    => $audience?->backUrl(),
        ]);
    }

    public function cancel(int $id): RedirectResponse
    {
        $back = redirect()->to(site_url('admin/group-emails/' . $id));

        try {
            $removed = $this->service->cancel($id);
        } catch (GroupEmailException $e) {
            return $back->with('error', $e->getMessage());
        }

        return $back->with('success', $removed
            ? $removed . ' unsent email' . ($removed === 1 ? '' : 's') . ' taken out of the queue.'
            : 'Nothing left to cancel: every copy has already gone.');
    }

    /* --------------------------------------------------------- */

    /** @return array{draft_id: ?int, subject: string, body_html: string, wrap_layout: bool, filter: array} */
    private function input(): array
    {
        $filter = $this->request->getPost('filter');

        return [
            'draft_id'    => (int) $this->request->getPost('draft_id') ?: null,
            'subject'     => (string) $this->request->getPost('subject'),
            'body_html'   => (string) $this->request->getPost('body_html'),
            'wrap_layout' => $this->request->getPost('wrap_layout') === '1',
            'filter'      => is_array($filter) ? $filter : [],
        ];
    }

    private function audienceOrFail(string $key, int $ref, array $input): Audience
    {
        return $this->service->audience($key, $ref, $input['filter'])
            ?? throw new GroupEmailException('Those recipients can\'t be found.');
    }

    private function admin(): array
    {
        return [
            'id'   => (int) session('admin_id') ?: null,
            'name' => session('admin_name'),
        ];
    }

    private function adminEmail(): string
    {
        $id = (int) session('admin_id');

        return $id ? (string) ((new UserModel())->find($id)['email'] ?? '') : '';
    }

    private function fail(string $message, int $status = 422): ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON(['ok' => false, 'message' => $message]);
    }
}
