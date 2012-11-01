<?php

namespace Application\Service;

use EdpGithub\Client;

class Repository
{
    /**
     * @var array
     */
    protected $repositories;

    /**
     * @var EdpGithub\ApiClient\ApiFactory
     */
    protected $api;


    public function setApi(Client $api)
    {
        $this->api = $api;
    }
}
