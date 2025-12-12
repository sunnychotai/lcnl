<?php

namespace App\Services;

use App\Models\MemberModel;
use App\Models\MembershipModel;
use App\Models\MemberAuditLogModel;
use CodeIgniter\Database\Exceptions\DataException;
use CodeIgniter\I18n\Time;

class MemberService
{
    protected MemberAuditLogModel $audit;

    public function __construct(
        private MemberModel $members = new MemberModel(),
        private MembershipModel $memberships = new MembershipModel(),
    ) {
        $this->audit = new MemberAuditLogModel();
    }

    /**
     * Normalise a UK mobile into +44xxxxxxxxxx if it begins with 0,
     * strip spaces/dashes/parentheses; keep leading + if provided
     */
    private function normaliseMobile(?string $mobile): ?string
    {
        if (!$mobile) {
            return null;
        }

        $m = preg_replace('/[^\d\+]/', '', $mobile);

        if (preg_match('/^0(\d{10})$/', $m, $m1)) {
            return '+44' . $m1[1];
        }

        if (preg_match('/^44(\d{9,12})$/', $m)) {
            return '+' . $m;
        }

        return $m;
    }

    /**
     * Create a pending member + membership
     */
    public function createPending(array $input): int
    {
        $mobile = $input['mobile'] ?? null;
        $mobile = $mobile !== '' ? $this->normaliseMobile($mobile) : null;

        $now = Time::now()->toDateTimeString();

        $data = [
            'first_name'    => trim($input['first_name']),
            'last_name'     => trim($input['last_name']),
            'email'         => strtolower(trim($input['email'])),
            'mobile'        => $mobile,
            'address1'      => trim($input['address1']),
            'address2'      => $input['address2'] !== '' ? trim($input['address2']) : null,
            'city'          => trim($input['city']),
            'postcode'      => strtoupper(trim($input['postcode'])),
            'date_of_birth' => $input['date_of_birth'] ?? null,
            'gender'        => $input['gender'] ?? null,
            'password_hash' => password_hash($input['password'], PASSWORD_DEFAULT),
            'status'        => 'pending',
            'consent_at'    => $now,
            'source'        => $input['source'] ?? 'web',
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        if (!$this->members->insert($data, true)) {
            throw new DataException('Unable to create member.');
        }

        $memberId = (int) $this->members->getInsertID();

        // Initial membership record
        $this->memberships->insert([
            'member_id'         => $memberId,
            'membership_type'   => 'Standard',
            'membership_number' => null,
            'start_date'        => date('Y-m-d'),
            'end_date'          => null,
            'status'            => 'active',
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);

        // 🔐 AUDIT
        $this->audit->insert([
            'member_id'   => $memberId,
            'type'        => 'member',
            'field_name'  => 'create',
            'old_value'   => null,
            'new_value'   => 'pending',
            'description' => 'Member created (pending) via registration',
            'changed_by'  => 0,
            'changed_at'  => $now,
        ]);

        return $memberId;
    }

    /**
     * Activate a member
     */
    public function activate(int $memberId, ?int $adminId = null): bool
    {
        $now = Time::now()->toDateTimeString();

        $ok = (bool) $this->members->update($memberId, [
            'status'      => 'active',
            'verified_at' => $now,
            'verified_by' => $adminId,
            'updated_at'  => $now,
        ]);

        if (!$ok) {
            return false;
        }

        // 🔐 AUDIT
        $this->audit->insert([
            'member_id'   => $memberId,
            'type'        => 'status',
            'field_name'  => 'status',
            'old_value'   => 'pending',
            'new_value'   => 'active',
            'description' => 'Member activated',
            'changed_by'  => $adminId ?? 0,
            'changed_at'  => $now,
        ]);

        return true;
    }

    /**
     * Disable a member
     */
    public function disable(int $memberId, ?int $adminId = null): bool
    {
        $now = Time::now()->toDateTimeString();

        $ok = (bool) $this->members->update($memberId, [
            'status'     => 'disabled',
            'updated_at' => $now,
        ]);

        if (!$ok) {
            return false;
        }

        // 🔐 AUDIT
        $this->audit->insert([
            'member_id'   => $memberId,
            'type'        => 'status',
            'field_name'  => 'status',
            'old_value'   => 'active',
            'new_value'   => 'disabled',
            'description' => 'Member disabled',
            'changed_by'  => $adminId ?? 0,
            'changed_at'  => $now,
        ]);

        return true;
    }

    /**
     * Reactivate a member
     */
    public function reactivate(int $memberId, ?int $adminId = null): bool
    {
        $now = Time::now()->toDateTimeString();

        $ok = (bool) $this->members->update($memberId, [
            'status'      => 'active',
            'verified_by' => $adminId,
            'updated_at'  => $now,
        ]);

        if (!$ok) {
            return false;
        }

        // 🔐 AUDIT
        $this->audit->insert([
            'member_id'   => $memberId,
            'type'        => 'status',
            'field_name'  => 'status',
            'old_value'   => 'disabled',
            'new_value'   => 'active',
            'description' => 'Member reactivated',
            'changed_by'  => $adminId ?? 0,
            'changed_at'  => $now,
        ]);

        return true;
    }
}
