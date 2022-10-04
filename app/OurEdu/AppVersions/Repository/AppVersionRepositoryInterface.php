<?php
declare(strict_types=1);

namespace App\OurEdu\AppVersions\Repository;

interface AppVersionRepositoryInterface
{
    public function get();

    /**
     * @param string $name
     * @return mixed
     */
    public function getByName(string $name);
}
