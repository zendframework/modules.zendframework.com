<?php

namespace ApplicationTest\Integration\Controller;

use Application\Controller;
use Application\Entity;
use Application\Service;
use ApplicationTest\Integration\Util\Bootstrap;
use ApplicationTest\Util\FakerTrait;
use stdClass;
use Zend\Http;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ContributorsControllerTest extends AbstractHttpControllerTestCase
{
    use FakerTrait;

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

        $contributors = $this->contributors(5);

        $repositoryRetriever
            ->expects($this->once())
            ->method('getContributors')
            ->with(
                $this->equalTo($vendor),
                $this->equalTo($name)
            )
            ->willReturn($contributors)
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

    /**
     * @link https://developer.github.com/v3/repos/statistics/#response
     *
     * @return stdClass
     */
    private function contributor()
    {
        $contributor = new stdClass();

        $contributor->total = $this->faker()->randomNumber();

        $contributor->author = new stdClass();

        $contributor->author->login = $this->faker()->unique()->userName;
        $contributor->author->avatar_url = $this->faker()->unique()->url;
        $contributor->author->html_url = $this->faker()->unique()->url;

        return $contributor;
    }

    /**
     * @param int $count
     * @return stdClass[]
     */
    private function contributors($count)
    {
        $contributors = [];

        for ($i = 0; $i < $count; $i++) {
            array_push($contributors, $this->contributor());
        }

        return $contributors;
    }
}
