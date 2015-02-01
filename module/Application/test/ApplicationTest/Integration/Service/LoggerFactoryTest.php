<?php

namespace ApplicationTest\Integration\Service;

use Application\Service;
use ApplicationTest\Integration\Util\Bootstrap;
use Monolog\Handler;
use Monolog\Logger;
use Psr\Log;
use PHPUnit_Framework_TestCase;

/**
 * @group Functional
 * @coversNothing
 */
class LoggerFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Logger
     */
    private $service;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->service = $serviceManager->get(Log\LoggerInterface::class);
    }

    public function testIsMonologLogger()
    {
        $this->assertInstanceOf(Logger::class, $this->service);
    }

    public function testName()
    {
        $this->assertSame('error-handling', $this->service->getName());
    }

    public function testHasRotatingHandler()
    {
        $handlers = $this->service->getHandlers();

        $hasRotatingFileHandler = false;

        array_walk($handlers, function ($handler) use (&$hasRotatingFileHandler) {
            if ($handler instanceof Handler\RotatingFileHandler) {
                $hasRotatingFileHandler = true;
            };
        });

        $this->assertTrue($hasRotatingFileHandler);
    }
}
