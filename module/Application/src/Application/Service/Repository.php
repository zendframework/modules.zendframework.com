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

    /**
     * Get All Repositories from github for authenticated user
     * @param  string $type
     * @return array
     */
    public function getAllRepository($type)
    {
        if(!isset($this->repository[$type])) {
            $client = $this->api;

            $this->repositories[$type] = $client->api('current_user')->repos();
        }
        return $this->repositories[$type];
    }

    /**
     * Recursively fetch all pages for Repositories
     * @param  array $repos
     * @param  string $params
     */
    protected function getRepositoriesRecursive(&$repos,  $params) {
        $service = $this->api->getService('Repo');

        $repos = array_merge($repos, $service->listRepositories(null, 'owner', $params));
        if($this->api->getNext() && $this->api->getNext() != $params['page']) {
            $params['page'] = $this->api->getNext();
            $this->getAllRepos($repos, $params);
        }
    }
    public function setApi(Client $api)
    {
        $this->api = $api;
    }
}