<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Who can use which part of the admin.
 *
 * ADMIN can use everything and isn't listed below. Every other role gets
 * only the features that list it. Routes check a feature with the `can`
 * filter ('filter' => 'can:gallery'), views with canAccess('gallery').
 *
 * To give a role another feature, add the role to that feature's list.
 */
class AdminAccess extends BaseConfig
{
    /**
     * Roles an admin login can have, with the label shown on the Users form.
     * A login whose role isn't here can't sign in.
     *
     * @var array<string, string>
     */
    public array $roles = [
        'ADMIN'      => 'Admin: everything',
        'WEBADMIN'   => 'Web admin: everything except users, emails and cron jobs',
        'WEBSITE'    => 'Website: events, FAQs and committees',
        'EVENTS'     => 'Events: events and event bookings',
        'MEMBERSHIP' => 'Membership: members and reports',
        'FINANCE'    => 'Finance: dashboard only',
    ];

    /**
     * Feature => roles (besides ADMIN) that can use it. An empty list means
     * ADMIN only. A feature that isn't listed is ADMIN only too.
     *
     * @var array<string, list<string>>
     */
    public array $features = [
        'dashboard'        => ['WEBADMIN', 'WEBSITE', 'EVENTS', 'MEMBERSHIP', 'FINANCE'],

        // ADMIN only: admin logins, the email queue, group emails and cron logs.
        'users'            => [],
        'email_queue'      => [],
        'group_emails'     => [],
        'cron'             => [],

        // /admin/membership
        'membership'       => ['WEBADMIN', 'MEMBERSHIP'],
        // /admin/content: events, event registrations, FAQs, committees, golf, Mela stalls
        'content'          => ['WEBADMIN', 'EVENTS', 'WEBSITE'],
        'chopda_pujan'     => ['WEBADMIN', 'EVENTS'],
        'christmas_mehfil' => ['WEBADMIN', 'EVENTS'],
        'connexions'       => ['WEBADMIN', 'EVENTS'],
        'legacy50'         => ['WEBADMIN'],
        'ticketing_survey' => ['WEBADMIN'],
        'gallery'          => ['WEBADMIN'],
    ];
}
