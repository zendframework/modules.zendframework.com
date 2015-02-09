<?php

namespace User;

use Zend\EventManager\SharedEventManager;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ApplicationInterface;
use ZfcBase\Module\AbstractModule;

class Module extends AbstractModule
{
    /**
     * @param ModuleManager $moduleManager
     * @param ApplicationInterface $app
     */
    public function bootstrap(ModuleManager $moduleManager, ApplicationInterface $app)
    {
        /* @var SharedEventManager $em */
        $em = $app->getEventManager()->getSharedManager();
        $sm = $app->getServiceManager();

        /* @var GitHub\LoginListener $loginListener */
        $loginListener = $sm->get(GitHub\LoginListener::class);

        $em->attachAggregate($loginListener);

        $em->attach('EdpGithub\Client', 'api', function ($e) use ($sm) {
            $hybridAuth = $sm->get('HybridAuth');
            $adapter = $hybridAuth->getAdapter('github');

            if ($adapter->isUserConnected()) {
                $token = $adapter->getAccessToken();
                $client = $e->getTarget();
                $client->authenticate('url_token', $token['access_token']);
            }
        });
    }

    public function getAutoloaderConfig()
    {
        return [];
    }

    public function getDir()
    {
        return __DIR__ . '/../..';
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }
}
