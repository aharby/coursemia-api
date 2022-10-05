<?php


namespace App\Modules\Config\Repository;


use App\Modules\Config\Config;

class ConfigRepository implements ConfigRepositoryInterface
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function get()
    {
        return $this->config->get();
    }

    public function getConfigsData()
    {
        return $this->config->get()->groupBy('type');
    }
}
