<?php
declare(strict_types=1);

namespace App\OurEdu\Config\Repository;

interface ConfigRepositoryInterface
{
    public function get();

    public function getConfigsData();
}
