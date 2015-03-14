<?php

namespace ZfModuleTest\Exception;

use PHPUnit_Framework_TestCase;
use Zend\Http;
use ZfModule\Controller\Exception;

class InvalidDataExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testCreateExceptionFromStaticMethodInvalidRequest()
    {
        $requestMock = $this->getMock(Http\Request::class);
        $requestMock
            ->expects($this->once())
            ->method('toString')
            ->willReturn('fooRequestAsString')
        ;

        $exception = Exception\InvalidDataException::fromInvalidRequest(
            'fooPublicMessage',
            $requestMock
        );

        $this->assertInstanceOf(Exception\InvalidDataException::class, $exception);
        $this->assertSame('fooPublicMessage', $exception->getPublicMessage());
        $this->assertSame('Invalid Request received [fooRequestAsString]', $exception->getMessage());
    }
}
