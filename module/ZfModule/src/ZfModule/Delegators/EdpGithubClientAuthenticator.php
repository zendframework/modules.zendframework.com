<?php
/**
 * Created by Gary Hockin.
 * Date: 22/01/2015
 * @GeeH
 */

namespace ZfModule\Delegators;

use EdpGithub\Client;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EdpGithubClientAuthenticator implements DelegatorFactoryInterface
{

    /**
     * A factory that creates delegates of a given service
     *
     * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
     * @param string $name the normalized service name
     * @param string $requestedName the requested service name
     * @param callable $callback the callback that is responsible for creating the service
     *
     * @return Client
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /** @var Client $client */
        $client = $callback();
        $config = $serviceLocator->get('config')['scn-social-auth'];
        $client->authenticate('UrlClientId', $config['github_client_id'], $config['github_secret']);

        return $client;
    }
}