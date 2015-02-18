<?php

namespace ApplicationTest\Integration\Controller;

use Application\Controller;
use ApplicationTest\Integration\Util\Bootstrap;
use Zend\Http;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use ZfModule\Mapper;

class SearchControllerTest extends AbstractHttpControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(Bootstrap::getConfig());
    }

    public function testIndexActionCanBeAccessed()
    {
        $moduleMapper = $this->getMockBuilder(Mapper\Module::class)->getMock();

        $moduleMapper
            ->expects($this->once())
            ->method('findByLike')
            ->with($this->equalTo(null))
            ->willReturn([])
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                Mapper\Module::class,
                $moduleMapper
            )
        ;

        $this->dispatch('/live-search');

        $this->assertControllerName(Controller\SearchController::class);
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }
}
