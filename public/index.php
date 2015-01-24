<?php
/**
* This makes our life easier when dealing with paths. Everything is relative
* to the application root now.
*/
chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

$appConfig = include 'config/application.config.php';

if (file_exists('config/development.config.php')) {
    $appConfig = Zend\Stdlib\ArrayUtils::merge($appConfig, include 'config/development.config.php');
}

// Run the application!
Zend\Mvc\Application::init($appConfig)->run();
