<?php

namespace App\Services;

use App\Models\FamilyModel;
use App\Models\FamilyMemberModel;
use App\Models\MemberModel;

class FamilyService
{
    public function __construct(
        private FamilyModel $families = new FamilyModel(),
        private FamilyMemberModel $links = new FamilyMemberModel(),
        private MemberModel $members = new MemberModel()
    ) {}

    /** Ensure a member has a family; if not, create one and set them as lead. */
    public function ensureLeadHousehold(int $memberId, ?array $address = null): int
    {
        // Do they already lead a household?
        $existing = $this->families->where('lead_member_id', $memberId)->first();
        if ($existing) return (int) $existing['id'];

        // Create new household
        $invite = $this->generateInviteCode();
        $data = [
            'lead_member_id' => $memberId,
            'household_name' => null,
            'invite_code'    => $invite,
        ];
        if ($address) $data = array_merge($data, $address);

        $this->families->insert($data, true);
        $familyId = (int) $this->families->getInsertID();

        // Link as lead
        if (! $this->links->isLinked($familyId, $memberId)) {
            $this->links->insert([
                'family_id' => $familyId,
                'member_id' => $memberId,
                'role'      => 'lead',
            ]);
        }

        return $familyId;
    }

    /** Add a dependent (child) to the household. Creates a member with a random password. */
    public function addDependent(int $familyId, array $input): int
    {
        // Create lightweight member (no email/mobile required)
        $random = bin2hex(random_bytes(8));
        $memberId = $this->members->insert([
            'first_name'    => trim($input['first_name']),
            'last_name'     => trim($input['last_name'] ?? ''),
            'email'         => $input['email'] ? strtolower(trim($input['email'])) : null,
            'mobile'        => $input['mobile'] ?: null,
            'postcode'      => $input['postcode'] ?? null,
            'password_hash' => password_hash($random, PASSWORD_DEFAULT), // placeholder
            'status'        => 'active', // dependents are considered active under lead
            'source'        => 'family_add',
        ], true);

        $this->links->insert([
            'family_id' => $familyId,
            'member_id' => (int) $memberId,
            'role'      => 'dependent',
        ]);

        return (int) $memberId;
    }

    /** Link an existing member by email to this family (e.g., spouse). */
    public function linkExistingByEmail(int $familyId, string $email, string $role = 'spouse'): bool
    {
        $m = $this->members->where('email', strtolower(trim($email)))->first();
        if (! $m) return false;

        if (! $this->links->isLinked($familyId, (int) $m['id'])) {
            $this->links->insert([
                'family_id' => $familyId,
                'member_id' => (int) $m['id'],
                'role'      => in_array($role, ['lead','spouse','dependent','other']) ? $role : 'other',
            ]);
        }
        return true;
    }

    /** Find a member’s household (lead or linked). */
    public function householdForMember(int $memberId): ?array
    {
        // lead?
        $family = $this->families->where('lead_member_id', $memberId)->first();
        if ($family) {
            $family['members'] = $this->links->membersOf((int) $family['id']);
            return $family;
        }
        // linked?
        $link = $this->links->where('member_id', $memberId)->first();
        if ($link) {
            $family = $this->families->find((int) $link['family_id']);
            if ($family) {
                $family['members'] = $this->links->membersOf((int) $family['id']);
                return $family;
            }
        }
        return null;
    }

    /** Generate a short uppercase invite code not in use. */
    private function generateInviteCode(): string
    {
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10)); // 10 chars
        } while ($this->families->where('invite_code', $code)->first());

        return $code;
    }
}
