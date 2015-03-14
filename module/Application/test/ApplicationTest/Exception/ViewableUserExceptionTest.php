<?php

namespace ApplicationTest\Exception;

use Application\Exception;
use PHPUnit_Framework_TestCase;

class ViewableUserExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testExceptionCanBeCreated()
    {
        $parentExceptionMock = $this->getMock(\Exception::class);

        $exception = new Exception\ViewableUserException(
            'fooMessage',
            'fooPublicMessage',
            666,
            $parentExceptionMock
        );

        $this->assertSame('fooMessage', $exception->getMessage());
        $this->assertSame('fooPublicMessage', $exception->getPublicMessage());
        $this->assertSame(666, $exception->getCode());
        $this->assertSame($parentExceptionMock, $exception->getPrevious());
    }
}
