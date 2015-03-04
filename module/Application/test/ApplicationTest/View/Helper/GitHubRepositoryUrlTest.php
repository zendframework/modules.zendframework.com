<?php

namespace ApplicationTest\View\Helper;

use Application\Entity;
use Application\View\Helper;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ReflectionObject;

class GitHubRepositoryUrlTest extends PHPUnit_Framework_TestCase
{
    public function testInvokeReturnsUrl()
    {
        $owner = 'foo';
        $name = 'bar';

        $url = sprintf(
            'https://github.com/%s/%s',
            $owner,
            $name
        );

        $repository = $this->repository(
            $owner,
            $name
        );

        $helper = new Helper\GitHubRepositoryUrl($repository);

        $this->assertSame($url, $helper());
    }

    public function testInvokeLazilyCreatesUrl()
    {
        $owner = 'foo';
        $name = 'bar';

        $url = 'https://example.org';

        $repository = $this->repository(
            $owner,
            $name
        );

        $helper = new Helper\GitHubRepositoryUrl($repository);

        $reflectionObject = new ReflectionObject($helper);

        $reflectionProperty = $reflectionObject->getProperty('url');
        $reflectionProperty->setAccessible(true);

        $reflectionProperty->setValue($helper, $url);

        $this->assertSame($url, $helper());
    }

    /**
     * @param string $owner
     * @param string $name
     * @return PHPUnit_Framework_MockObject_MockObject|Entity\Repository
     */
    private function repository($owner, $name)
    {
        $repository = $this->getMockBuilder(Entity\Repository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repository
            ->expects($this->any())
            ->method('owner')
            ->willReturn($owner)
        ;

        $repository
            ->expects($this->any())
            ->method('name')
            ->willReturn($name)
        ;

        return $repository;
    }
}
