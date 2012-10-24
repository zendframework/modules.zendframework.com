<?php

namespace Application\Service;

use EdpGithub\ApiClient\ApiClient;

class Repository
{
    /**
     * @var array
     */
    protected $repository;

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
            echo $this->api->getOauthToken();
            exit;
            $service = $this->api->getService('Repo');
            $params['per_page'] = 100;
            $params['page'] = 1;
            $this->repositories[$type] = $service->listRepositories(null, $type, $params);
            if($this->api->getNext() && $this->api->getNext() != $this->api->getCurrent()) {
                $params['page'] = $this->api->getNext();
                $this->getRepositoriesRecursive($this->repositories[$type], $params);
            }
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
    public function setApi(ApiClient $api)
    {
        $this->api = $api;
    }
}