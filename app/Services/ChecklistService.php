<?php

namespace App\Services;

use App\Models\MemberModel;

class ChecklistService
{
    public function __construct(
        private MemberModel $members = new MemberModel(),
        private FamilyService $family = new FamilyService()
    ) {}

    /**
     * Returns ['todo' => [...], 'done' => [...]]
     */
public function memberTasks(int $memberId): array
{
    $m = $this->members->find($memberId);
    if (!$m) {
        return [
            'todo' => [],
            'done' => [],
        ];
    }
    $todo = [];
    $done = [];

    // 1) Household exists?
    $house = $this->family->householdForMember($memberId);
    if ($house) {
        $done[] = $this->task(
            'household_created',
            'Household created',
            'Manage your household and add family members.',
            'bi-house-heart-fill',
            route_to('account.household')
        );

        // 1a) Has at least one additional member?
        $memberCount = count($house['members'] ?? []);
        if ($memberCount < 2) {
            $todo[] = $this->task(
                'household_add_members',
                'Add your family',
                'Link your spouse or add children to your household.',
                'bi-people-fill',
                route_to('account.household')
            );
        } else {
            $done[] = $this->task(
                'household_add_members',
                'Family linked',
                'Your household has multiple members.',
                'bi-people-fill',
                route_to('account.household')
            );
        }
    } else {
        $todo[] = $this->task(
            'household_created',
            'Create your household',
            'Set yourself as the lead and manage your family in one place.',
            'bi-house-add-fill',
            route_to('account.household')
        );
    }

    // 2) Mobile number
    $urlProfile = route_to('account.profile.edit');
    if (!empty($m['mobile'])) {
        $done[] = $this->task('mobile', 'Mobile number added', 'We can reach you for event updates.', 'bi-phone-fill', $urlProfile);
    } else {
        $todo[] = $this->task('mobile', 'Add your mobile number', 'Helps with last-minute event updates and SMS alerts.', 'bi-phone-fill', $urlProfile);
    }

    // 3) Postcode
    if (!empty($m['postcode'])) {
        $done[] = $this->task('postcode', 'Postcode added', 'Helps us plan local programmes.', 'bi-geo-alt-fill', $urlProfile);
    } else {
        $todo[] = $this->task('postcode', 'Add your postcode', 'Helps us plan local programmes and insights.', 'bi-geo-alt-fill', $urlProfile);
    }

    // 4) Consent
    if (!empty($m['consent_at'])) {
        $done[] = $this->task('consent', 'Consent recorded', 'You’ll receive LCNL communications.', 'bi-check2-circle', $urlProfile);
    } else {
        $todo[] = $this->task('consent', 'Give communication consent', 'Stay informed about news and events.', 'bi-envelope-fill', $urlProfile);
    }

    // 0) Email verification
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


    return ['todo' => $todo, 'done' => $done];
}


    private function task(string $id, string $title, string $desc, string $icon, string $url): array
    {
        return compact('id','title','desc','icon','url');
    }
}
