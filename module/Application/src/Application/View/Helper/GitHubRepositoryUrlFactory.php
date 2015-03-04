<?php

namespace Application\View\Helper;

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

        $config = $serviceManager->get('Config')['zf_modules']['repository'];

        return new GitHubRepositoryUrl(
            $config['owner'],
            $config['name']
        );
    }
}
