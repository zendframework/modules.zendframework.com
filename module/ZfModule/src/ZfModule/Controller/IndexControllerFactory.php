<?php

namespace ZfModule\Controller;

use EdpGithub\Client;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfModule\Mapper;

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

        /* @var Mapper\Module $moduleMapper */
        $moduleMapper = $serviceManager->get('zfmodule_mapper_module');

        /* @var Client $githubClient */
        $githubClient = $serviceManager->get('EdpGithub\Client');

        return new IndexController(
            $moduleMapper,
            $githubClient
        );
    }
}
