<?php

namespace ApplicationTest\Integration\Controller;

use Application\Controller;
use Application\Entity;
use Application\Service;
use ApplicationTest\Integration\Util\Bootstrap;
use stdClass;
use Zend\Http;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ContributorsControllerTest extends AbstractHttpControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(Bootstrap::getConfig());
    }

    public function testContributorsActionCanBeAccessed()
    {
        $vendor = 'foo';
        $name = 'bar';

        $repository = $this->getMockBuilder(Entity\Repository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repository
            ->expects($this->any())
            ->method('owner')
            ->willReturn($vendor)
        ;

        $repository
            ->expects($this->any())
            ->method('name')
            ->willReturn($name)
        ;

        $repositoryRetriever = $this->getMockBuilder(Service\RepositoryRetriever::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getContributors')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn([])
        ;

        $metaData = new stdClass();
        $metaData->forks_count = 200;
        $metaData->stargazers_count = 250;
        $metaData->watchers_count = 300;

        $repositoryRetriever
            ->expects($this->once())
            ->method('getUserRepositoryMetadata')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn($metaData)
        ;

        $this->getApplicationServiceLocator()
            ->setAllowOverride(true)
            ->setService(
                'project_github_repository',
                $repository
            )
            ->setService(
                Service\RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $this->dispatch('/contributors');

        $this->assertMatchedRouteName('contributors');

        $this->assertControllerName(Controller\ContributorsController::class);
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }
}
