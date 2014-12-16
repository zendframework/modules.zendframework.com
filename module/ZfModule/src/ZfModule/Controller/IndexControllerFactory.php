<?php

namespace ZfModule\Controller;

use Application\Service\RepositoryRetriever;
use EdpGithub\Client;
use Zend\Cache;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfModule\Mapper;
use ZfModule\Service;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $controllerManager
     * @return IndexController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /* @var ControllerManager $controllerManager */
        $serviceManager = $controllerManager->getServiceLocator();

        /* @var Cache\Storage\StorageInterface $moduleCache */
        $moduleCache = $serviceManager->get('zfmodule_cache');

        /* @var Mapper\Module $moduleMapper */
        $moduleMapper = $serviceManager->get('zfmodule_mapper_module');

        /* @var Service\Module $moduleService */
        $moduleService = $serviceManager->get('zfmodule_service_module');

        /* @var Client $githubClient */
        $githubClient = $serviceManager->get('EdpGithub\Client');

        /* @var RepositoryRetriever $repositoryRetriever */
        $repositoryRetriever = $serviceManager->get('RepositoryRetriever');

        return new IndexController(
            $moduleCache,
            $moduleMapper,
            $moduleService,
            $githubClient,
            $repositoryRetriever
        );
    }
}
