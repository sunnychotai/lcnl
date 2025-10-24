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
            'emails' => $emails,
            'pager'  => $model->pager,
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

    public function delete(int $id)
    {
        $model = new EmailQueueModel();
        $model->delete($id);

        return redirect()->to('/admin/system/emails')->with('message', 'Email deleted.');
    }
}
