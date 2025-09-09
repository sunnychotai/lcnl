<?php

namespace App\Controllers;
use App\Models\FaqModel;
use App\Models\CommitteeModel;

class Home extends BaseController
{
    public function index()
    {
        $events = [
            ['date' => '2025-09-20', 'title' => 'Navratri Night 1', 'venue' => 'DLC'],
            ['date' => '2025-09-21', 'title' => 'Navratri Night 2', 'venue' => 'DLC'],
            ['date' => '2025-10-26', 'title' => 'Diwali Celebration', 'venue' => 'RCT'],
        ];
        return view('home', compact('events'));
    }

    public function events()  { return view('events'); }

        public function mahila() { 
        
        $committeeModel = new CommitteeModel();
        $members = $committeeModel
        ->where('committee', 'Mahila')   // 👈 filter
        ->orderBy('id', 'ASC')              // or whatever column you use
        ->findAll();
        return view('mahila', [
            'members' => $members
        ]);
    }
    public function gallery() { return view('gallery'); }
    public function contact() { return view('contact'); }
    public function bereavement()
        {
            $faqModel = new FaqModel();
            $data['faqs'] = $faqModel->getByGroup('Bereavement'); // <-- pull from DB
            return view('bereavement', $data); // <-- point to your updated bereavement view
        }    
    
    public function membership() { return view('membership'); }

    public function aboutus() { return view('aboutus'); }


    public function committee() { 
        
        $committeeModel = new CommitteeModel();
        $members = $committeeModel
        ->where('committee', 'Executive')   // 👈 filter
        ->orderBy('id', 'ASC')              // or whatever column you use
        ->findAll();
        return view('committee', [
            'members' => $members
        ]);
    }

    public function faq()
{
    $faqModel = new \App\Models\FaqModel();
    $data['groupedFaqs'] = $faqModel->getGrouped(); // fetch grouped FAQs

    return view('faqs/index', $data);
}

}
