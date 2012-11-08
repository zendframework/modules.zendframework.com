<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;

class Module
{
    public function onBootstrap($e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'application_module_mapper' => function ($sm) {
                    $mapper = new Mapper\Module();
                    $mapper->setDbAdapter($sm->get('zfcuser_zend_db_adapter'));
                    $mapper->setEntityPrototype(new Entity\Module);
                    $mapper->setHydrator(new Mapper\ModuleHydrator());
                    return $mapper;
                },
                'application_module_service' => function($sm) {
                    $service = new  Service\Module;
                    return $service;
                },
                'application_service_repository' => function($sm) {
                    $service = new Service\Repository;
                    $service->setApi($sm->get('EdpGithub\Client'));
                    return $service;
                },
                'github_client' => function($sm) {
                    $hybridAuth = $sm->get('HybridAuth');
                    $adapter = $hybridAuth->getAdapter('github');
                    $token = $adapter->getAccessToken();

                    $client = $sm->get('EdpGithubClient');
                    $client->authenticate('url_token',$token['access_token'], null);
                    return $client;
                }
            ),
        );
    }
}
