<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
//use App\Models\FamilyMemberModel;

class FamiliesController extends BaseController
{
    public function merge()
    {
        // TODO: implement: move members from source_family_id -> target_family_id, then delete source
        // Keep for Phase 2.
        return redirect()->back()->with('message', 'Merge tool coming soon.');
    }
}
