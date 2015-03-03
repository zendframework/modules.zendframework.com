<?php

namespace ApplicationTest\View\Helper;

use Application\View\Helper;
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

        $helper = new Helper\GitHubRepositoryUrl(
            $owner,
            $name
        );

        $this->assertSame($url, $helper());
    }

    public function testInvokeLazilyCreatesUrl()
    {
        $owner = 'foo';
        $name = 'bar';

        $url = 'https://example.org';

        $helper = new Helper\GitHubRepositoryUrl(
            $owner,
            $name
        );

        $reflectionObject = new ReflectionObject($helper);

        $reflectionProperty = $reflectionObject->getProperty('url');
        $reflectionProperty->setAccessible(true);

        $reflectionProperty->setValue($helper, $url);

        $this->assertSame($url, $helper());
    }
}
