<?php

namespace App\Controllers;
use App\Models\EventModel;

class Sitemap extends BaseController
{
    public function index()
    {
        $eventModel = new EventModel();
        $events = $eventModel->orderBy('event_date', 'DESC')->findAll();

        $xml = view('sitemap/static'); // base static entries
        $xml .= "<!-- Dynamic Events -->\n";

        foreach ($events as $event) {
            $url = base_url('events/' . $event['id']);
            $lastmod = !empty($event['updated_at']) ? date('Y-m-d', strtotime($event['updated_at'])) : date('Y-m-d');
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url}</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>yearly</changefreq>\n";
            $xml .= "    <priority>0.6</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>";

        return $this->response->setHeader('Content-Type', 'application/xml')
                              ->setBody($xml);
    }
}
