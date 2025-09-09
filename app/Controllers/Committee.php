<?php namespace App\Controllers;

use App\Models\CommitteeModel;

class Committee extends BaseController
{
    public function index()
    {
        $committeeModel = new CommitteeModel();
        $members = $committeeModel->getAllOrdered();

        return view('committee', [
            'members' => $members
        ]);
    }
}