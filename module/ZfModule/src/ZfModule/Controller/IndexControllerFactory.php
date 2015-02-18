<?php

namespace ZfModule\Controller;

use Application\Service\RepositoryRetriever;
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

        /* @var Mapper\Module $moduleMapper */
        $moduleMapper = $serviceManager->get(Mapper\Module::class);

        /* @var Service\Module $moduleService */
        $moduleService = $serviceManager->get(Service\Module::class);

        /* @var RepositoryRetriever $repositoryRetriever */
        $repositoryRetriever = $serviceManager->get(RepositoryRetriever::class);

        return new IndexController(
            $moduleMapper,
            $moduleService,
            $repositoryRetriever
        );
    }
}
