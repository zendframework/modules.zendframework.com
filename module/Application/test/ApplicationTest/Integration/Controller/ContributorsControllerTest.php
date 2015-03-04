<?php

namespace ApplicationTest\Integration\Controller;

use Application\Controller;
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

        $config = $this->getApplicationServiceLocator()->get('Config');

        $config['github_repository'] = [
            'repository' => [
                'owner' => $vendor,
                'name'  => $name,
            ],
        ];

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
                'Config',
                $config
            )
            ->setService(
                Service\RepositoryRetriever::class,
                $repositoryRetriever
            )
        ;

        $this->dispatch('/contributors');

        $this->assertControllerName(Controller\ContributorsController::class);
        $this->assertActionName('index');
        $this->assertResponseStatusCode(Http\Response::STATUS_CODE_200);
    }
}
