<?php


namespace App\OurEdu\AppVersions\Repository;

use App\OurEdu\AppVersions\AppVersion;

class AppVersionRepository implements AppVersionRepositoryInterface
{
    private $appVersion;

    public function __construct(AppVersion $appVersion)
    {
        $this->appVersion = $appVersion;
    }

    public function get()
    {
        return $this->appVersion->get();
    }


    /**
     * @inheritDoc
     */
    public function getByName(string $name)
    {
        return $this->appVersion->where('name',$name)->get();
    }
}
