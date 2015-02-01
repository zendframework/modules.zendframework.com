<?php

namespace ApplicationTest\Service;

use Application\Service;
use Exception;
use PHPUnit_Framework_TestCase;
use Zend\Log;

class ErrorHandlingServiceTest extends PHPUnit_Framework_TestCase
{
    public function testLogExceptionLogsSomething()
    {
        $exception = new Exception('Why, hello!');

        $logger = $this->getMockBuilder(Log\Logger::class)->getMock();
        $logger
            ->expects($this->once())
            ->method('err')
            ->with($this->stringContains($exception->getMessage()))
        ;

        $service = new Service\ErrorHandlingService($logger);
        $service->logException($exception);
    }
}
