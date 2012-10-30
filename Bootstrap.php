<?php
namespace EdpGithubTest;

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

class Bootstrap
{
    protected static $serviceManager;

    public static function init()
    {
        // Load the user-defined test configuration file, if it exists; otherwise, load
        $config = include __DIR__ . '/config/application.config.php';

        static::initAutoloader();

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    protected static function initAutoloader()
    {
        $loader = include __DIR__ . '/init_autoloader.php';

        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true,
                'namespaces' => array(
                    'ApplicationTest' => __DIR__ . '/module/Application/test/ApplicationTest',
                    'UserTest' => __DIR__ . '/module/User/test/UserTest',
                ),
            ),
        ));
    }
}

Bootstrap::init();
