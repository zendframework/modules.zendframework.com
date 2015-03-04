<?php

namespace ApplicationTest\Integration\Service;

use Application\Entity;
use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;

/**
 * @group Functional
 * @coversNothing
 */
class GitHubRepositoryTest extends PHPUnit_Framework_TestCase
{
    public function testServiceCanBeRetrieved()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->assertInstanceOf(
            Entity\Repository::class,
            $serviceManager->get('github_repository')
        );
    }
}
