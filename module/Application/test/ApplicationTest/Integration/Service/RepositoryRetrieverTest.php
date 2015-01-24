<?php

namespace ApplicationTest\Integration\Service;

use Application\Service;
use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;

class RepositoryRetrieverTest extends PHPUnit_Framework_TestCase
{
    public function testServiceCanBeRetrieved()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->assertInstanceOf(
            Service\RepositoryRetriever::class,
            $serviceManager->get(Service\RepositoryRetriever::class)
        );
    }
}
