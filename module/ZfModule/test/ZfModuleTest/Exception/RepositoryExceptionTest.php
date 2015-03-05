<?php

namespace ZfModuleTest\Exception;

use PHPUnit_Framework_TestCase;
use Zend\Http;
use ZfModule\Entity;
use ZfModule\Controller\Exception;

class RepositoryExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testCreateExceptionFromNotFoundRepository()
    {
        $exception = Exception\RepositoryException::fromNotFoundRepository(
            'fooPublicMessage',
            'fooOwner',
            'fooName'
        );

        $this->assertInstanceOf(Exception\RepositoryException::class, $exception);
        $this->assertSame('fooPublicMessage', $exception->getPublicMessage());
        $this->assertSame('Invalid Repository requested [fooOwner/fooName]', $exception->getMessage());
        $this->assertSame(Http\Response::STATUS_CODE_404, $exception->getCode());
    }

    public function testCreateExceptionFromNotFoundRepositoryUrl()
    {
        $exception = Exception\RepositoryException::fromNotFoundRepositoryUrl(
            'fooPublicMessage',
            'fooRepositoryUrl'
        );

        $this->assertInstanceOf(Exception\RepositoryException::class, $exception);
        $this->assertSame('fooPublicMessage', $exception->getPublicMessage());
        $this->assertSame('Invalid Repository from URL requested [fooRepositoryUrl]', $exception->getMessage());
        $this->assertSame(Http\Response::STATUS_CODE_404, $exception->getCode());
    }

    public function testCreateExceptionFromInsufficientPermissions()
    {
        $exception = Exception\RepositoryException::fromInsufficientPermissions(
            'fooPublicMessage',
            'foo/name',
            ['fooPermission', 'barPermission']
        );

        $this->assertInstanceOf(Exception\RepositoryException::class, $exception);
        $this->assertSame('fooPublicMessage', $exception->getPublicMessage());
        $this->assertSame('Invalid Repository permission [foo/name] required [fooPermission,barPermission]', $exception->getMessage());
        $this->assertSame(Http\Response::STATUS_CODE_403, $exception->getCode());
    }

    public function testCreateExceptionFromNonModuleRepository()
    {
        $exception = Exception\RepositoryException::fromNonModuleRepository(
            'fooPublicMessage',
            'foo/name'
        );

        $this->assertInstanceOf(Exception\RepositoryException::class, $exception);
        $this->assertSame('fooPublicMessage', $exception->getPublicMessage());
        $this->assertSame('Invalid Repository - No ZF Module [foo/name]', $exception->getMessage());
        $this->assertSame(Http\Response::STATUS_CODE_403, $exception->getCode());
    }
}
