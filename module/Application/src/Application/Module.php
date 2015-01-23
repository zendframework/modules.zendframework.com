<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Service\ErrorHandlingService;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

class Module
{
    public function onBootstrap($e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // Attach logger for exceptions
        $eventManager->attach('dispatch.error', function (MvcEvent $event) {
            $exception = $event->getResult()->exception;
            if ($exception) {
                $sm      = $event->getApplication()->getServiceManager();
                $service = $sm->get('ApplicationServiceErrorHandling');
                $service->logException($exception);
            }
        });
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'ApplicationServiceErrorHandling' => function (ServiceManager $sm) {
                    $logger  = $sm->get('ZendLog');
                    $service = new ErrorHandlingService($logger);
                    return $service;
                },
                'ZendLog'                         => function (ServiceManager $sm) {
                    $filename = 'log_' . date('F') . '.txt';
                    $log      = new Logger();
                    $writer   = new Stream('./data/logs/' . $filename);
                    $log->addWriter($writer);
                    return $log;
                },
            ],
        ];
    }


    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
