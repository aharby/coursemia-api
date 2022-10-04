<?php

namespace App\OurEdu\Invitations\Repository;

use App\OurEdu\Invitations\Models\Invitation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InvitationRepository implements InvitationRepositoryInterface
{

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return Invitation::jsonPaginate(env('PAGE_LIMIT', 20));
    }

    /**
     * @param int $invitation_id
     * @return Invitation|null
     */
    public function find(int $invitation_id): ?Invitation
    {
        return Invitation::find($invitation_id);
    }


    /**
     * @param string $email
     * @return Invitation|null
     */
    public function findByEmail(string $email): ?Invitation
    {
        return Invitation::where('receiver_email', $email)->first();
    }

    /**
     * @param Invitation $invitation
     * @param array $data
     * @return bool
     */
    public function update(Invitation $invitation, array $data): bool
    {
        return $invitation->update($data);
    }

    /**
     * @param $id
     * @return Invitation|null
     */
    public function findOrFail($id): ?Invitation
    {
        return Invitation::findOrFail($id);
    }

    public function create(array $data, $invitable): Invitation
    {
        $data = array_merge([
            'invitable_type'    =>  get_class($invitable),
            'invitable_id'    =>  $invitable->id
        ], $data);

        return Invitation::create($data);
    }

    /**
     * @param Invitation $invitation
     * @return bool
     */
    public function delete(Invitation $invitation): bool
    {
        return $invitation->delete();
    }

    /**
     * @param string $email
     * @return Invitation|null
     */
    public function findByEmailAndSender(string $email, int $senderId): ?Invitation
    {
        return Invitation::where('receiver_email', $email)
            ->where('sender_id', $senderId)->first();
    }

    /**
     * @param string $email
     * @param int $senderId
     * @param array $status
     * @return Invitation|null
     */
    public function findByEmailAndSenderWhereStatusIn(string $email, int $senderId, $status=[]): ?Invitation
    {
        return Invitation::where('receiver_email', $email)
            ->whereIn('status', $status)
            ->where('sender_id', $senderId)->first();
    }
}
