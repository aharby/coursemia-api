<?php

declare(strict_types=1);

namespace App\OurEdu\Payments\Gateways\UrWayService;

use GuzzleHttp\Client;

abstract class BaseService
{
    /**
     * Store guzzle client instance.
     *
     * @var UrWayClient
     */
    protected $guzzleClient;

    /**
     * URWAY payment base path.
     *
     * @var string
     */
    protected $basePath ;

    /**
     * Store URWAY payment endpoint.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * BaseService Constructor.
     */
    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    /**
     * @return string
     */
    public function getEndPointPath()
    {
        return $this->getBasePath() . '/' . $this->endpoint;
    }


    protected function getBasePath()
    {
        return $this->basePath = config('urway.auth.base_bath');
    }
}
