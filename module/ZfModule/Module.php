<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZfModule;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Cache\StorageFactory;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
            // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap($e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'zfmodule_cache' => function($sm) {
                    $config = $sm->get('Config');
                    $storage = StorageFactory::factory($config['zfmodule']['cache']);

                    return $storage;
                },
                'zfmodule_mapper_module' => function ($sm) {
                    $mapper = new Mapper\Module();
                    $mapper->setDbAdapter($sm->get('zfcuser_zend_db_adapter'));
                    $mapper->setEntityPrototype(new Entity\Module);
                    $mapper->setHydrator(new Mapper\ModuleHydrator());
                    return $mapper;
                },
                'zfmodule_service_module' => function($sm) {
                    $service = new  Service\Module;
                    return $service;
                },
                'zfmodule_service_repository' => function($sm) {
                    $service = new Service\Repository;
                    $service->setApi($sm->get('EdpGithub\Client'));
                    return $service;
                },
                /*'github_client' => function($sm) {
                    $hybridAuth = $sm->get('HybridAuth');
                    $adapter = $hybridAuth->getAdapter('github');
                    $token = $adapter->getAccessToken();

                    $client = $sm->get('EdpGithubClient');
                    $client->authenticate('url_token',$token['access_token'], null);
                    return $client;
                }*/
            ),
        );
    }
}
