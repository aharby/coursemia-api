<?php

namespace App\OurEdu\Invitations\Enums;

class InvitationEnums
{
    const ACCEPTED = 'accepted',
        CANCELED = 'canceled',
        REFUSED = 'refused',
        PENDING = 'pending';

    public static function getAcceptInvationUrl($invitation)
    {
        return env('APP_URL') . '/invitation_url/' . $invitation->id;
    }

    public static function getReceiverStatusActions()
    {
        return [
            self::REFUSED,
            self::ACCEPTED
        ];
    }

    public static function getSenderStatusActions()
    {
        return [
            self::CANCELED,
        ];
    }

    public static function getReceiverAvailableStatuses()
    {
        return [
            self::PENDING
        ];
    }

    public static function getSenderAvailableStatuses()
    {
        return [
            self::PENDING
        ];
    }
}
