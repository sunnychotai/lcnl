<?php

namespace App\Services;

use App\Models\MemberModel;

class ChecklistService
{
    public function __construct(
        private MemberModel $members = new MemberModel(),
    ) {}

    /**
     * Returns ['todo' => [...], 'done' => [...]]
     */
    public function memberTasks(int $memberId): array
    {
        $m = $this->members->find($memberId);
        if (!$m) {
            return ['todo' => [], 'done' => []];
        }

        $todo = [];
        $done = [];

        $urlProfile = route_to('account.profile.edit');

        // 1) Email verification
        if (!empty($m['verified_at'])) {
            $done[] = $this->task(
                'verified_at',
                'Email verified',
                'Your email is confirmed.',
                'bi-envelope-check-fill',
                '#'
            );
        } else {
            $todo[] = $this->task(
                'verified_at',
                'Verify your email',
                'Please confirm your email to unlock access.',
                'bi-envelope-exclamation-fill',
                base_url('membership/resend-verification?email=' . urlencode($m['email']))
            );
        }

        // 2) Mobile number
        if (!empty($m['mobile'])) {
            $done[] = $this->task(
                'mobile',
                'Mobile number added',
                'We can reach you for event updates.',
                'bi-phone-fill',
                $urlProfile
            );
        } else {
            $todo[] = $this->task(
                'mobile',
                'Add your mobile number',
                'Helps with last-minute event updates and SMS alerts.',
                'bi-phone-fill',
                $urlProfile
            );
        }

        // 3) Address (Address1 + City + Postcode)
        if (!empty($m['address1']) && !empty($m['city']) && !empty($m['postcode'])) {
            $done[] = $this->task(
                'address',
                'Address added',
                'Your contact details are complete.',
                'bi-geo-alt-fill',
                $urlProfile
            );
        } else {
            $todo[] = $this->task(
                'address',
                'Add your address',
                'Please provide Address Line 1, City, and Postcode.',
                'bi-geo-alt-fill',
                $urlProfile
            );
        }

        // 4) Consent
        if (!empty($m['consent_at'])) {
            $done[] = $this->task(
                'consent',
                'Consent recorded',
                'You’ll receive LCNL communications.',
                'bi-check2-circle',
                $urlProfile
            );
        } else {
            $todo[] = $this->task(
                'consent',
                'Give communication consent',
                'Stay informed about news and events.',
                'bi-envelope-fill',
                $urlProfile
            );
        }

        return ['todo' => $todo, 'done' => $done];
    }

    private function task(string $id, string $title, string $desc, string $icon, string $url): array
    {
        return compact('id','title','desc','icon','url');
    }
}
