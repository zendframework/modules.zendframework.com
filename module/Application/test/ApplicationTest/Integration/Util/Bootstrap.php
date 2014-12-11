<?php

namespace ApplicationTest\Integration\Util;

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

class Bootstrap
{
    protected static $serviceManager;

    public static function init()
    {
        // Load the user-defined test configuration file, if it exists; otherwise, load
        $config = include __DIR__ . '/../../../../../../config/application.config.php';

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
    }

    /**
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        return static::$serviceManager;
    }
}
