<?php

namespace App\Controllers;
use App\Models\EventModel;

class Sitemap extends BaseController
{
    public function index()
    {
        $eventModel = new EventModel();
        // Only published events — an unpublished event 404s, and listing it
        // in the sitemap invites search engines to crawl a dead URL.
        $events = $eventModel
            ->where('is_valid', 1)
            ->orderBy('event_date', 'DESC')
            ->findAll();

        // debug => false: in non-production environments the view renderer wraps
        // output in <!-- DEBUG-VIEW --> comments, which would push the XML
        // declaration off line 1 and make the sitemap invalid.
        $xml = view('sitemap/static', [], ['debug' => false]); // base static entries
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
