<?php

namespace Application\Controller;

use Application\Entity;
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

        /* @var Entity\Repository $repository */
        $repository = $serviceManager->get('github_repository');

        return new ContributorsController(
            $repositoryRetriever,
            $repository
        );
    }
}
