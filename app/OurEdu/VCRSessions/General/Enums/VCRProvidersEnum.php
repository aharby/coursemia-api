<?php


namespace App\OurEdu\VCRSessions\General\Enums;


final class VCRProvidersEnum
{
    const AGORA = 'agora';
    const ZOOM  = 'zoom';

    public static function getList(): array
    {
        return [
            self::AGORA => trans(self::AGORA),
            self::ZOOM => trans(self::ZOOM),
        ];
    }

    public static function getDefaultProvider(): string
    {
        return env('DEFAULT_MEETING_PROVIDER', self::ZOOM);
    }
}
