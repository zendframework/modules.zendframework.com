<?php

namespace Application\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class Repository implements ServiceManagerAwareInterface
{
    protected $serviceManager;
    
    protected $repository;

    /**
     * Get All Repositories from github for authenticated user
     * @param  string $type 
     * @return array
     */
    public function getAllRepository($type)
    {
        if(!isset($this->repository[$type])) {
            $sm = $this->getServiceManager();
            $api = $sm->get('edpgithub_api_factory');
            $service = $api->getService('Repo');

            $params['per_page'] = 100;
            $params['page'] = 1;
            $this->repositories[$type] = $service->listRepositories(null, $type, $params);
            if($api->getNext() && $api->getNext() != $api->getCurrent()) {
                $params['page'] = $api->getNext();
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
        $sm = $this->getServiceManager();
        $api = $sm->get('edpgithub_api_factory');
        $service = $api->getService('Repo');

        $repos = array_merge($repos, $service->listRepositories(null, 'owner', $params));
        if($api->getNext() && $api->getNext() != $params['page']) {
            $params['page'] = $api->getNext();
            $this->getAllRepos($repos, $params);
        }
    }

    /**
     * Get Service Manager
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
    
    /**
     * Set ServiceManager
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}