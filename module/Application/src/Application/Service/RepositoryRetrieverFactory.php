<?php

namespace Application\Service;

use EdpGithub\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RepositoryRetrieverFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return RepositoryRetriever
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var Client $githubClient */
        $githubClient = $serviceLocator->get('EdpGithub\Client');

        return new RepositoryRetriever($githubClient);
    }
}
