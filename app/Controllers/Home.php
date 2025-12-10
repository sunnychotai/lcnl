<?php

namespace App\Controllers;

use App\Models\FaqModel;
use App\Models\EventModel;

class Home extends BaseController
{
    public function index()
    {

        $eventModel = new EventModel();

        $data = [
            'upcomingEvents' => $eventModel->getUpcomingEvents([], 10),

            // SAFE: we never call session() directly
            'isLoggedIn' => session()->get('isMemberLoggedIn') ?? false,
            'memberName' => session()->get('member_name') ?? null,
        ];

        return view('home', $data);
    }

    public function accessDenied()
    {
        return view('errors/access_denied', [
            'title' => 'Access Denied',
            'message' => 'You do not have permission to access this page.'
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
        return view('services/bereavement', [
            'faqs' => $faqModel->getByGroup('Bereavement')
        ]);
    }

    public function tabletennis()
    {
        return view('services/tabletennis');
    }

    public function membership()
    {
        $memberId = session()->get('member_id');

        $membership = null;

        if ($memberId) {
            $membershipModel = new \App\Models\MembershipModel();

            $membership = $membershipModel
                ->where('member_id', $memberId)
                ->orderBy('id', 'DESC')
                ->first();

            // Default to Standard + Active if no membership record exists
            if (!$membership) {
                $membership = [
                    'membership_type' => 'Standard',
                    'status' => 'active'
                ];
            }
        }

        return view('membership/index', [
            'membership' => $membership,
        ]);
    }



    public function aboutus()
    {
        return view('aboutus');
    }

    public function faq()
    {
        $faqModel = new FaqModel();
        return view('faqs/index', [
            'groupedFaqs' => $faqModel->getGrouped(),
        ]);
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
