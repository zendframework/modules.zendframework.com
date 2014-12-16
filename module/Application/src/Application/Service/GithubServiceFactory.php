<?php

namespace Application\Service;

use EdpGithub\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GithubServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return Module
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var Client $githubClient */
        $githubClient = $serviceLocator->get('EdpGithub\Client');

        return new GithubService($githubClient);
    }
}
