<?php

namespace ApplicationTest\View\Helper;

use Application\View\Helper;
use PHPUnit_Framework_TestCase;

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
}
