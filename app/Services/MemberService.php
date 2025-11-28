<?php

namespace App\Services;

use App\Models\MemberModel;
use App\Models\MembershipModel;
use CodeIgniter\Database\Exceptions\DataException;
use CodeIgniter\I18n\Time;

class MemberService
{
    public function __construct(
        private MemberModel $members = new MemberModel(),
        private MembershipModel $memberships = new MembershipModel(),
    ) {
    }

    /**
     * Normalise a UK mobile into +44xxxxxxxxxx if it begins with 0,
     * strip spaces/dashes/parentheses; keep leading + if provided
     */
    private function normaliseMobile(?string $mobile): ?string
    {
        if (!$mobile)
            return null;
        $m = preg_replace('/[^\d\+]/', '', $mobile);

        // If starts with 0 and has 11 digits, convert to +44
        if (preg_match('/^0(\d{10})$/', $m, $m1)) {
            return '+44' . $m1[1];
        }
        // If starts with 44 (no +), add +
        if (preg_match('/^44(\d{9,12})$/', $m, $m2)) {
            return '+' . $m;
        }
        return $m;
    }

    /**
     * Create a pending member + pending membership + optional family rows.
     * Returns member ID.
     */
    public function createPending(array $input): int
    {
        $mobile = $input['mobile'] ?? null;
        $mobile = $mobile !== '' ? $this->normaliseMobile($mobile) : null;

        $data = [
            'first_name' => trim($input['first_name']),
            'last_name' => trim($input['last_name']),
            'email' => strtolower(trim($input['email'])),
            'mobile' => $mobile,
            'address1' => trim($input['address1']),
            'address2' => $input['address2'] !== '' ? trim($input['address2']) : null,
            'city' => trim($input['city']),
            'postcode' => strtoupper(trim($input['postcode'])),
            'date_of_birth' => $input['date_of_birth'] ?? null,
            'gender' => $input['gender'] ?? null,

            'password_hash' => password_hash($input['password'], PASSWORD_DEFAULT),
            'status' => 'pending',
            'consent_at' => date('Y-m-d H:i:s'),
            'source' => $input['source'] ?? 'web',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!$this->members->insert($data, true)) {
            throw new DataException('Unable to create member.');
        }

        $memberId = (int) $this->members->getInsertID();

        // Create blank/pending membership record
        $this->memberships->insert([
            'member_id' => $memberId,
            'membership_type' => null,            // ❗ Admin assigns later
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $memberId;
    }


    /**
     * Activate a member (email verified) and activate membership accordingly.
     * For 'life' membership we set start_date today and no end_date.
     * For 'annual'/'senior'/'youth' we set start_date today and end_date +1 year.
     */
    public function activate(int $memberId, ?int $adminId = null): bool
    {
        // Activate member
        $ok = (bool) $this->members->update($memberId, [
            'status' => 'active',
            'verified_at' => Time::now()->toDateTimeString(),
            'verified_by' => $adminId,
            'updated_at' => Time::now()->toDateTimeString(),
        ]);

        if (!$ok)
            return false;

        // Membership remains "pending" until ADMIN sets membership_type
        return true;
    }


    public function disable(int $memberId): bool
    {
        return (bool) $this->members->update($memberId, [
            'status' => 'disabled',
            'updated_at' => Time::now()->toDateTimeString(),
        ]);
    }

    public function reactivate(int $memberId, ?int $adminId = null): bool
    {
        return (bool) $this->members->update($memberId, [
            'status' => 'active',
            'verified_by' => $adminId,
            'updated_at' => Time::now()->toDateTimeString(),
        ]);
    }
}
