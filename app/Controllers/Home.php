<?php

namespace App\Controllers;

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
    public function gallery() { return view('gallery'); }
    public function contact() { return view('contact'); }
    public function bereavement()  { return view('bereavement'); }
    public function membership() { return view('membership'); }
}
