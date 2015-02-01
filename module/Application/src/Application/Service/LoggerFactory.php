<?php

namespace Application\Service;

use Monolog\Handler;
use Monolog\Logger;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Logger
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $handler = new Handler\RotatingFileHandler('data/logs/error.log');
        $handler->setFilenameFormat(
            '{filename}-{date}',
            'Y-m'
        );

        $logger = new Logger('error-handling');
        $logger->pushHandler($handler);

        return $logger;
    }
}
