<?php

namespace ZfModule\Delegators;

use EdpGithub\Client;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EdpGithubClientAuthenticator implements DelegatorFactoryInterface
{

    /**
     * {@inheritDoc}
     *
     * @return Client
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /* @var Client $client */
        $client = $callback();
        $config = $serviceLocator->get('config')['scn-social-auth'];
        if (array_key_exists('github_client_id', $config) && array_key_exists('github_secret', $config)) {
            $client->authenticate('UrlClientId', $config['github_client_id'], $config['github_secret']);
        }

        return $client;
    }
}
