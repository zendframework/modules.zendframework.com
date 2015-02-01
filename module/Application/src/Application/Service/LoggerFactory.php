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
        $handler = new Handler\RotatingFileHandler('data/logs/log.txt');
        $handler->setFilenameFormat(
            '{filename}_{date}',
            'F'
        );

        $logger = new Logger('error-handling');
        $logger->pushHandler($handler);

        return $logger;
    }
}
