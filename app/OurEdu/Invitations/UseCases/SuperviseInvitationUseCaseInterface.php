<?php

namespace App\OurEdu\Invitations\UseCases;

interface SuperviseInvitationUseCaseInterface
{
    /**
     * Send supervise invitation
     * @param string $email
     * @return void
     */
    public function superviseInvitation($email, $type, array $subjects, bool $abilitiesUser = false);

    /**
     * @param $invitationId
     * @return mixed
     */
    public function resendSuperviseInvitation($invitationId, $type, bool $abilitiesUser = false);

    /**
     * Accept | Refuse invitation
     * @param integer $id
     * @return mixed
     */
    public function changeStatus($id);

    /**
     * Cancel invitation
     * @param integer $id
     * @return mixed
     */
    public function cancelInviation($id);

    /**
     * Remove parent or student relation
     * @param integer $id
     * @return mixed
     */
    public function removeRelation($id);


    /**
     * @param $email
     * @return mixed
     */
    public function checkIfSentBeforeWherePending($email);

    public function validate($request): array;

}
