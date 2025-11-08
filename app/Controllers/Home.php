<?php

namespace App\Controllers;

use App\Models\FaqModel;
use App\Models\CommitteeModel;
use App\Models\EventModel;

class Home extends BaseController
{
    public function index()
    {
        $eventModel = new EventModel();
        $data['upcomingEvents'] = $eventModel->getUpcomingEvents([], 10);

        return view('home', $data);
    }

    public function accessDenied()
    {
        return view('errors/access_denied', [
            'title' => 'Access Denied',
            'message' => 'You do not have permission to access this page. Please contact an administrator if you believe this is an error.'
        ]);
    }


    public function gallery()
    {
        return view('gallery');
    }
    public function contact()
    {
        return view('contact');
    }
    public function bereavement()
    {
        $faqModel = new FaqModel();
        $data['faqs'] = $faqModel->getByGroup('Bereavement'); // <-- pull from DB
        return view('services/bereavement', $data); // <-- point to your updated bereavement view
    }

    public function tabletennis()
    {
        return view('services/tabletennis');
    }

    public function membership()
    {
        return view('membership/index');
    }

    public function aboutus()
    {
        return view('aboutus');
    }


    public function faq()
    {
        $faqModel = new \App\Models\FaqModel();
        $data['groupedFaqs'] = $faqModel->getGrouped(); // fetch grouped FAQs

        return view('faqs/index', $data);
    }

    public function sample()
    {
        return view('sample');
    }

    public function privacy()
    {
        return view('privacy');
    }
}
