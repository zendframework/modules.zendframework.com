<?php

namespace ApplicationTest\Integration\Controller;

use ApplicationTest\Integration\Util\Bootstrap;
use Zend\Http;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(Bootstrap::getConfig());
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/');

        $this->assertControllerName('Application\Controller\Index');
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }
}
