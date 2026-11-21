<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EmailQueueModel;

class Emails extends BaseController
{
    public function index()
    {
        $model = new EmailQueueModel();
        $emails = $model->orderBy('id', 'DESC')->paginate(20);

        return view('admin/system/emails/index', [
            'emails'    => $emails,
            'pager'     => $model->pager,
            'retryable' => $this->retryableFailed()->countAllResults(),
        ]);
    }

    public function view(int $id)
    {
        $model = new EmailQueueModel();
        $email = $model->find($id);

        if (! $email) {
            return redirect()->to('/admin/system/emails')->with('error', 'Email not found.');
        }

        return view('admin/system/emails/view', ['email' => $email]);
    }

    public function retry(int $id)
    {
        $model = new EmailQueueModel();
        $email = $model->find($id);

        if ($email) {
            $model->update($id, [
                'status'     => 'pending',
                'last_error' => null,
            ]);
            return redirect()->to('/admin/system/emails')->with('message', 'Email queued for retry.');
        }

        return redirect()->to('/admin/system/emails')->with('error', 'Email not found.');
    }

    /**
     * Put every failed email back in the queue in one go. emails:send then
     * works through them at its usual rate, and pauses itself if SMTP is
     * still failing. Invalid addresses and permanent rejections (e.g. spam)
     * would only fail again, so they stay as they are.
     */
    public function retryFailed()
    {
        $count = $this->retryableFailed()->update([
            'status'       => 'pending',
            'last_error'   => null,
            'scheduled_at' => null,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]) ? db_connect()->affectedRows() : 0;

        $back = redirect()->to('/admin/system/emails');

        return $count > 0
            ? $back->with('message', $count . ' failed email' . ($count === 1 ? '' : 's') . ' queued again.')
            : $back->with('error', 'There are no failed emails to retry.');
    }

    /** Failed emails worth retrying (see EmailQueueModel::retryableFailed()). */
    private function retryableFailed(): \CodeIgniter\Database\BaseBuilder
    {
        return (new EmailQueueModel())->retryableFailed();
    }

    public function delete(int $id)
    {
        $model = new EmailQueueModel();
        $model->delete($id);

        return redirect()->to('/admin/system/emails')->with('message', 'Email deleted.');
    }
}
