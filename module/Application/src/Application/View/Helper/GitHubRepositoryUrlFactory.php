<?php

namespace Application\View\Helper;

use Application\Entity;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

class GitHubRepositoryUrlFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return GitHubRepositoryUrl
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var HelperPluginManager $serviceLocator */
        $serviceManager = $serviceLocator->getServiceLocator();

        /* @var Entity\Repository $repository */
        $repository = $serviceManager->get('project_github_repository');

        return new GitHubRepositoryUrl($repository);
    }
}
