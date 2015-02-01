<?php

namespace Application\Service;

use Zend\Log;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ErrorHandlingServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return ErrorHandlingService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Log\Logger $logger */
        $logger  = $serviceLocator->get('ZendLog');

        return new ErrorHandlingService($logger);
    }
}
