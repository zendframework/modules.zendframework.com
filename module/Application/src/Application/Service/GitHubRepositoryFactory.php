<?php

namespace Application\Service;

use Application\Entity;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GitHubRepositoryFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Entity\Repository
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config')['project_github_repository'];

        return new Entity\Repository(
            $config['owner'],
            $config['name']
        );
    }
}
