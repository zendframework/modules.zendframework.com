<?php

namespace ApplicationTest\Service;

use Application\Service;
use Exception;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers Application\Service\ErrorHandlingService
 */
class ErrorHandlingServiceTest extends PHPUnit_Framework_TestCase
{
    public function testLogExceptionLogsSomething()
    {
        $exception = new Exception('Why, hello!');

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo($exception->getMessage()),
                $this->equalTo($exception->getTrace())
            )
        ;

        $service = new Service\ErrorHandlingService($logger);
        $service->logException($exception);
    }
}
