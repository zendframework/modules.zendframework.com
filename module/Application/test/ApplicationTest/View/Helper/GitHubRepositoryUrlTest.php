<?php

namespace ApplicationTest\View\Helper;

use Application\View\Helper;
use ApplicationTest\Mock\StringCastable;
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

    public function testConstructorCastsArgumentsToString()
    {
        $owner = new StringCastable('foo');
        $name = new StringCastable('bar');

        $helper = new Helper\GitHubRepositoryUrl(
            $owner,
            $name
        );

        $properties = [
            'owner' => $owner,
            'name' => $name,
        ];

        $reflectionObject = new ReflectionObject($helper);

        array_walk($properties, function ($value, $name) use ($reflectionObject, $helper) {
            $reflectionProperty = $reflectionObject->getProperty($name);
            $reflectionProperty->setAccessible(true);

            $this->assertSame((string) $value, $reflectionProperty->getValue($helper));
        });
    }
}
