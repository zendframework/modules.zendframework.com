<?php

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

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
                $service = $sm->get(Service\ErrorHandlingService::class);
                $service->logException($exception);
            }
        });
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
