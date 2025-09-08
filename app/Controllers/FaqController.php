<?php

namespace App\Controllers;

use App\Models\FaqModel;

class FaqController extends BaseController
{
    public function index()
    {
        $faqModel = new FaqModel();
        $data['groupedFaqs'] = $faqModel->getGrouped();

        return view('faq/index', $data);
    }

    public function group($group)
    {
        $faqModel = new FaqModel();
        $data['faqs'] = $faqModel->getByGroup($group);
        $data['groupName'] = ucfirst($group);

        return view('faq/group', $data);
    }

    public function all()
    {
        $faqModel = new FaqModel();
        $data['faqs'] = $faqModel->getByGroup();

        return view('faq/all', $data);
    }

    public function bereavement()
{
    $faqModel = new \App\Models\FaqModel();
    $data['faqs'] = $faqModel->getByGroup('Bereavement');

    return view('bereavement/index', $data);
}

}
