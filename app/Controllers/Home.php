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

        $data['metaDescription'] = 'Lohana Community North London – bringing people together since 1976. Events, membership, committees and community services in North London.';

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
        return view('gallery', [
            'title'           => 'Gallery',
            'metaDescription' => 'Photographs from LCNL events, festivals and community gatherings.',
        ]);
    }

    public function contact()
    {
        return view('contact', [
            'title'           => 'Contact Us',
            'metaDescription' => 'Get in touch with Lohana Community North London — enquiries, hall hire and committee contacts.',
        ]);
    }

    public function bereavement()
    {
        $faqModel = new FaqModel();
        return view('services/bereavement', [
            'title'           => 'Bereavement Support',
            'metaDescription' => 'LCNL bereavement support — guidance and contacts to help members and families at a difficult time.',
            'faqs'            => $faqModel->getByGroup('Bereavement'),
        ]);
    }

    public function tabletennis()
    {
        return view('services/tabletennis', [
            'title'           => 'Table Tennis',
            'metaDescription' => 'Join the LCNL table tennis sessions — open to members of all ages and abilities.',
        ]);
    }

    public function dlcHire()
    {
        return view('services/dlc_hire', [
            'title'           => 'Hall Hire',
            'metaDescription' => 'Hire the Dharmaj Lohana Centre for weddings, celebrations and community events in North London.',
        ]);
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
            'title'           => 'Membership',
            'metaDescription' => 'Become a member of Lohana Community North London — Standard and Life membership options, benefits and how to join.',
            'membership'      => $membership,
        ]);
    }



    public function aboutus()
    {
        return view('aboutus', [
            'title'           => 'About Us',
            'metaDescription' => 'The story of Lohana Community North London — our history since 1976, our values and the people who run it.',
        ]);
    }

    public function faq()
    {
        $faqModel = new FaqModel();
        return view('faqs/index', [
            'title'           => 'Frequently Asked Questions',
            'metaDescription' => 'Answers to common questions about LCNL membership, events, hall hire and community services.',
            'groupedFaqs'     => $faqModel->getGrouped(),
        ]);
    }

    public function privacy()
    {
        return view('privacy', [
            'title'           => 'Privacy Policy',
            'metaDescription' => 'How Lohana Community North London collects, stores and uses your personal data.',
        ]);
    }
}
