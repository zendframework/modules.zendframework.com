<?php

namespace Application\Service;

use Psr\Log\LoggerInterface;
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
        /* @var LoggerInterface $logger */
        $logger  = $serviceLocator->get(LoggerInterface::class);

        return new ErrorHandlingService($logger);
    }
}
