<?php

namespace App\Controllers;

use App\Models\FaqModel;

class FaqController extends BaseController
{
    public function index()
    {
        $faqModel = new FaqModel();
        $data['title'] = 'Frequently Asked Questions';
        $data['metaDescription'] = 'Answers to common questions about LCNL membership, events, hall hire and community services.';
        $data['groupedFaqs'] = $faqModel->getGrouped();

        return view('faqs/index', $data);
    }

    public function group($group)
    {
        $faqModel = new FaqModel();
        $data['faqs'] = $faqModel->getByGroup($group);
        $data['groupName'] = ucfirst($group);
        $data['title'] = $data['groupName'] . ' FAQs';
        $data['metaDescription'] = 'LCNL frequently asked questions about ' . strtolower($data['groupName']) . '.';

        return view('faqs/group', $data);
    }

    public function all()
    {
        $faqModel = new FaqModel();
        $data['title'] = 'All FAQs';
        $data['metaDescription'] = 'Every frequently asked question about Lohana Community North London, in one place.';
        $data['faqs'] = $faqModel->getByGroup();

        return view('faqs/all', $data);
    }

    /**
     * Legacy URL. The bereavement page is served by Home::bereavement at /bereavement;
     * the view this used to render (bereavement/index) does not exist, so this route
     * returned a 500. Redirect rather than 404 so existing links keep working.
     */
    public function bereavement()
    {
        return redirect()->to(site_url('bereavement'), 301);
    }


}
