<?php

namespace App\OurEdu\Invitations\Repository;

use App\OurEdu\Invitations\Models\Invitation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InvitationRepositoryInterface
{
    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator;


    /**
     * @param int $invitation_id
     * @return Invitation|null
     */
    public function find(int $invitation_id): ?Invitation;


    /**
     * @param string $email
     * @return Invitation|null
     */
    public function findByEmail(string $email): ?Invitation;

    /**
     * @param Invitation $invitation
     * @param array $data
     * @return bool
     */
    public function update(Invitation $invitation, array $data): bool;

    /**
     * @param $id
     * @return Invitation|null
     */
    public function findOrFail($id): ?Invitation;

    public function create(array $data, $invitable): Invitation;


    /**
     * @param Invitation $invitation
     * @return bool
     */
    public function delete(Invitation $invitation): bool;

    /**
     * @param string $email
     * @param int $senderId
     * @return Invitation|null
     */
    public function findByEmailAndSender(string $email, int $senderId): ?Invitation;

    /**
     * @param string $email
     * @param int $senderId
     * @param array $status
     * @return Invitation|null
     */
    public function findByEmailAndSenderWhereStatusIn(string $email, int $senderId,$status=[]): ?Invitation;


}
