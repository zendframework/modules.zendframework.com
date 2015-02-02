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
    public function testLogExceptionLogsExceptionWithTrace()
    {
        $exception = new Exception('Why, hello!');

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo($exception->getMessage()),
                $this->equalTo([
                    'previous' => [],
                    'trace' => $exception->getTrace(),
                ])
            )
        ;

        $service = new Service\ErrorHandlingService($logger);
        $service->logException($exception);
    }

    public function testLogExceptionLogsExceptionWithPreviousExceptionTrace()
    {
        $a = new Exception('Oh noes');
        $b = new Exception('What have I done?!', 0, $a);
        $c = new Exception('This is hard', 0, $b);

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo($c->getMessage()),
                $this->equalTo([
                    'previous' => [
                        $b->getMessage(),
                        $a->getMessage(),
                    ],
                    'trace' => $c->getTrace(),
                ])
            )
        ;

        $service = new Service\ErrorHandlingService($logger);
        $service->logException($c);
    }
}
