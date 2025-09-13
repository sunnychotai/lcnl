<?php

namespace App\Services;

use App\Models\MemberModel;
use CodeIgniter\Database\Exceptions\DataException;

class MemberService
{
    public function __construct(private MemberModel $members = new MemberModel())
    {
    }

    /**
     * Create a pending member from validated input.
     */
    public function createPending(array $input): int
    {
        $data = [
            'first_name'    => trim($input['first_name']),
            'last_name'     => trim($input['last_name']),
            'email'         => strtolower(trim($input['email'])),
            'mobile'        => $input['mobile'] !== '' ? $input['mobile'] : null,
            'postcode'      => $input['postcode'] !== '' ? $input['postcode'] : null,
            'password_hash' => password_hash($input['password'], PASSWORD_DEFAULT),
            'status'        => 'pending',
            'consent_at'    => date('Y-m-d H:i:s'),
            'source'        => $input['source'] ?? 'web',
        ];

        if (! $this->members->insert($data, true)) {
            throw new DataException('Unable to create member.');
        }

        return (int) $this->members->getInsertID();
    }

    public function activate(int $memberId, ?int $adminId = null): bool
    {
        return (bool) $this->members->update($memberId, [
            'status'      => 'active',
            'verified_at' => date('Y-m-d H:i:s'),
            'verified_by' => $adminId,
        ]);
    }

    public function disable(int $memberId): bool
    {
        return (bool) $this->members->update($memberId, ['status' => 'disabled']);
    }

    public function list(string $status = 'pending', ?string $q = null, int $limit = 50): array
    {
        return $this->members->listByStatus($status, $q, $limit);
    }

    public function counts(): array
    {
        return $this->members->countsByStatus();
    }
}
