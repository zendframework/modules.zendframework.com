<?php

namespace ApplicationTest\Integration\View\Helper;

use Application\View\Helper;
use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;
use Zend\View\HelperPluginManager;

class GitHubRepositoryUrlTest extends PHPUnit_Framework_TestCase
{
    public function testServiceCanBeRetrieved()
    {
        $serviceManager = Bootstrap::getServiceManager();

        /* @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $serviceManager->get('ViewHelperManager');

        $this->assertInstanceOf(
            Helper\GitHubRepositoryUrl::class,
            $viewHelperManager->get('githubRepositoryUrl')
        );
    }
}
