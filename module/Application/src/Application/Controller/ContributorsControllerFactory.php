<?php

namespace Application\Controller;

use Application\Service\RepositoryRetriever;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ContributorsControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceManager = $serviceLocator->getServiceLocator();
        $repositoryRetriever = $serviceManager->get(RepositoryRetriever::class);
        $repositoryData = $serviceManager->get('Config')['zf-modules']['repository'];

        return new ContributorsController($repositoryRetriever, $repositoryData);
    }
}
