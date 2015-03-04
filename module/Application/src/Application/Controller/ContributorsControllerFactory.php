<?php

namespace Application\Controller;

use Application\Service;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ContributorsControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $controllerManager
     * @return ContributorsController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /* @var ControllerManager $controllerManager */
        $serviceManager = $controllerManager->getServiceLocator();

        /* @var Service\RepositoryRetriever $repositoryRetriever */
        $repositoryRetriever = $serviceManager->get(Service\RepositoryRetriever::class);

        $repositoryData = $serviceManager->get('Config')['zf_modules']['repository'];

        return new ContributorsController(
            $repositoryRetriever,
            $repositoryData
        );
    }
}
