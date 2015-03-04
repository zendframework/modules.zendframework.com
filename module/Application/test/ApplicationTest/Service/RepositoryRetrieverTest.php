<?php

namespace ApplicationTest\Service;

use Application\Service\RepositoryRetriever;
use ApplicationTest\Util\FakerTrait;
use EdpGithub;
use EdpGithub\Api;
use EdpGithub\Client;
use EdpGithub\Collection;
use EdpGithub\Listener\Exception;
use PHPUnit_Framework_TestCase;
use stdClass;

class RepositoryRetrieverTest extends PHPUnit_Framework_TestCase
{
    use FakerTrait;

    public function testCanRetrieveUserRepositories()
    {
        $payload = [
            ['name' => 'foo'],
            ['name' => 'bar'],
            ['name' => 'baz'],
        ];

        $client = $this->getClientMock(
            new Api\User(),
            $payload
        );

        $service = new RepositoryRetriever($client);

        $repositories = $service->getUserRepositories('foo');

        $this->assertInstanceOf(Collection\RepositoryCollection::class, $repositories);

        $count = 0;
        foreach ($repositories as $repository) {
            $this->assertEquals(current($payload), (array) $repository);
            next($payload);
            ++$count;
        }

        $this->assertEquals(count($payload), $count);
    }

    public function testCanRetrieveUserRepositoryMetadata()
    {
        $payload = [
            'name' => 'foo',
            'url' => 'http://foo.com',
        ];

        $client = $this->getClientMock(
            new Api\Repos(),
            $payload
        );

        $service = new RepositoryRetriever($client);

        $metadata = $service->getUserRepositoryMetadata('foo', 'bar');

        $this->assertInstanceOf('stdClass', $metadata);
        $this->assertEquals($payload, (array) $metadata);
    }

    public function testCanRetrieveRepositoryFileContent()
    {
        $payload = [
            'content' => base64_encode('foo'),
        ];

        $client = $this->getClientMock(
            new Api\Repos(),
            $payload
        );

        $service = new RepositoryRetriever($client);

        $response = $service->getRepositoryFileContent('foo', 'bar', 'foo.baz');

        $this->assertEquals('foo', $response);
    }

    public function testRepositoryContentCanParsedMarkdown()
    {
        $content = 'repository file __FOO__ content';
        $markdown = function ($content) {
            return str_replace('__FOO__', 'bar', $content);
        };

        $apiMock = $this->getMock(Api\Markdown::class, ['content', 'render']);
        $apiMock
            ->expects($this->once())
            ->method('render')
            ->with($this->equalTo($content))
            ->willReturn($markdown($content));

        $apiMock
            ->expects($this->any())
            ->method('content')
            ->willReturn(json_encode(['content' => base64_encode($content)]));

        $clientMock = $this->getMock(EdpGithub\Client::class);
        $clientMock->expects($this->any())
            ->method('api')
            ->willReturn($apiMock);

        $service = new RepositoryRetriever($clientMock);
        $contentMarkdown = $service->getRepositoryFileContent('foo', 'bar', 'foo.md', true);

        $this->assertEquals('repository file bar content', $contentMarkdown);
    }

    public function testRepositoryContentMarkdownFails()
    {
        $content = 'repository file __FOO__ content';
        $apiMock = $this->getMock(Api\Markdown::class, ['content', 'render']);
        $apiMock
            ->expects($this->once())
            ->method('render')
            ->willThrowException(new Exception\RuntimeException());

        $apiMock
            ->expects($this->any())
            ->method('content')
            ->willReturn(json_encode(['content' => base64_encode($content)]));

        $clientMock = $this->getMock(EdpGithub\Client::class);
        $clientMock->expects($this->any())
            ->method('api')
            ->willReturn($apiMock);

        $service = new RepositoryRetriever($clientMock);
        $contentMarkdown = $service->getRepositoryFileContent('foo', 'bar', 'foo.md', true);

        $this->assertNull($contentMarkdown);
    }

    public function testResponseContentMissingOnGetRepositoryFileContent()
    {
        $payload = [];

        $client = $this->getClientMock(
            new Api\Repos(),
            $payload
        );

        $service = new RepositoryRetriever($client);
        $response = $service->getRepositoryFileContent('foo', 'bar', 'baz');

        $this->assertFalse($response);
    }

    public function testCanRetrieveRepositoryFileMetadata()
    {
        $payload = [
            'name' => 'foo',
            'url' => 'http://foo.com',
        ];

        $client = $this->getClientMock(
            new Api\Repos(),
            $payload
        );

        $service = new RepositoryRetriever($client);

        $metadata = $service->getRepositoryFileMetadata('foo', 'bar', 'baz');

        $this->assertInstanceOf('stdClass', $metadata);
        $this->assertEquals($payload, (array) $metadata);
    }

    public function testCanRetrieveAuthenticatedUserRepositories()
    {
        $payload = [
            ['name' => 'foo'],
            ['name' => 'bar'],
            ['name' => 'baz'],
        ];

        $client = $this->getClientMock(
            new Api\CurrentUser(),
            $payload
        );

        $service = new RepositoryRetriever($client);

        $repositories = $service->getAuthenticatedUserRepositories();

        $this->assertInstanceOf(Collection\RepositoryCollection::class, $repositories);

        $count = 0;
        foreach ($repositories as $repository) {
            $this->assertEquals(current($payload), (array) $repository);
            next($payload);
            ++$count;
        }

        $this->assertEquals(count($payload), $count);
    }

    public function testRepositoryFileContentFails()
    {
        $clientMock = $this->getMock('EdpGithub\Client');
        $clientMock->expects($this->any())
            ->method('api')
            ->willThrowException(new Exception\RuntimeException());

        $service = new RepositoryRetriever($clientMock);
        $response = $service->getRepositoryFileContent('foo', 'bar', 'baz');
        $this->assertFalse($response);
    }

    public function testRepositoryDoesNotExists()
    {
        $clientMock = $this->getMock('EdpGithub\Client');
        $clientMock->expects($this->any())
            ->method('api')
            ->willThrowException(new Exception\RuntimeException());

        $service = new RepositoryRetriever($clientMock);
        $response = $service->getUserRepositoryMetadata('foo', 'bar');
        $this->assertFalse($response);
    }

    public function testGetContributorsFetchesFromApi()
    {
        $owner = 'foo';
        $name = 'bar';

        $repositoryApi = $this->getMockBuilder(Api\Repos::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $response = json_encode($this->contributors(5));

        $repositoryApi
            ->expects($this->once())
            ->method('contributors')
            ->with(
                $this->equalTo($owner),
                $this->equalTo($name)
            )
            ->willReturn($response)
        ;

        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $client
            ->expects($this->once())
            ->method('api')
            ->with($this->equalTo('repos'))
            ->willReturn($repositoryApi)
        ;

        $service = new RepositoryRetriever($client);

        $service->getContributors(
            $owner,
            $name
        );
    }

    /**
     * @param Api\AbstractApi $apiInstance
     * @param array $payload
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getClientMock(Api\AbstractApi $apiInstance, array $payload = [])
    {
        $response = $this->getMock('Zend\Http\Response');

        $response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn(json_encode($payload))
        ;

        $headers = $this->getMock('Zend\Http\Headers');

        $response
            ->expects($this->any())
            ->method('getHeaders')
            ->willReturn($headers)
        ;

        $httpClient = $this->getMock('EdpGithub\Http\Client');

        $httpClient
            ->expects($this->any())
            ->method('get')
            ->willReturn($response)
        ;

        $client = $this->getMock('EdpGithub\Client');

        $client
            ->expects($this->any())
            ->method('getHttpClient')
            ->willReturn($httpClient)
        ;

        $apiInstance->setClient($client);

        $client
            ->expects($this->any())
            ->method('api')
            ->willReturn($apiInstance)
        ;

        return $client;
    }

    public function testGetContributorsDecodesResponseToArray()
    {
        $owner = 'foo';
        $name = 'bar';

        $repositoryApi = $this->getMockBuilder(Api\Repos::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $response = json_encode($this->contributors(10));

        $repositoryApi
            ->expects($this->once())
            ->method('contributors')
            ->with(
                $this->equalTo($owner),
                $this->equalTo($name)
            )
            ->willReturn($response)
        ;

        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $client
            ->expects($this->once())
            ->method('api')
            ->with($this->equalTo('repos'))
            ->willReturn($repositoryApi)
        ;

        $service = new RepositoryRetriever($client);

        $contributors = $service->getContributors(
            $owner,
            $name
        );

        $this->assertInternalType('array', $contributors);

        array_walk($contributors, function ($contributor) {
            $this->assertInternalType('array', $contributor);
            $this->assertArrayHasKey('login', $contributor);
            $this->assertArrayHasKey('avatar_url', $contributor);
            $this->assertArrayHasKey('html_url', $contributor);
        });
    }

    public function testGetContributorsReturnsContributorsInReverseOrder()
    {
        $owner = 'foo';
        $name = 'bar';

        $repositoryApi = $this->getMockBuilder(Api\Repos::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $contributorsAsReturned = $this->contributors(10);

        $response = json_encode($contributorsAsReturned);

        $repositoryApi
            ->expects($this->once())
            ->method('contributors')
            ->with(
                $this->equalTo($owner),
                $this->equalTo($name)
            )
            ->willReturn($response)
        ;

        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $client
            ->expects($this->once())
            ->method('api')
            ->with($this->equalTo('repos'))
            ->willReturn($repositoryApi)
        ;

        $service = new RepositoryRetriever($client);

        $contributors = $service->getContributors(
            $owner,
            $name
        );

        $this->assertInternalType('array', $contributors);
        $this->assertCount(count($contributorsAsReturned), $contributors);

        array_walk($contributors, function ($contributor) use (&$contributorsAsReturned) {

            $expectedContributor = array_pop($contributorsAsReturned);

            $this->assertInternalType('array', $contributor);

            $this->assertArrayHasKey('login', $contributor);
            $this->assertSame($expectedContributor->login, $contributor['login']);

            $this->assertArrayHasKey('avatar_url', $contributor);
            $this->assertSame($expectedContributor->avatar_url, $contributor['avatar_url']);

            $this->assertArrayHasKey('html_url', $contributor);
            $this->assertSame($expectedContributor->html_url, $contributor['html_url']);
        });
    }

    public function testGetContributorsReturnsFalseIfRuntimeExceptionIsThrown()
    {
        $owner = 'foo';
        $name = 'bar';

        $repositoryApi = $this->getMockBuilder(Api\Repos::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryApi
            ->expects($this->once())
            ->method('contributors')
            ->willThrowException(new Exception\RuntimeException())
        ;

        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $client
            ->expects($this->once())
            ->method('api')
            ->with($this->equalTo('repos'))
            ->willReturn($repositoryApi)
        ;

        $service = new RepositoryRetriever($client);

        $contributors = $service->getContributors(
            $owner,
            $name
        );

        $this->assertFalse($contributors);
    }

    /**
     * @link https://developer.github.com/v3/repos/#response-5
     *
     * @return stdClass
     */
    private function contributor()
    {
        $contributor = new stdClass();

        $contributor->login = $this->faker()->unique()->userName;
        $contributor->avatar_url = $this->faker()->unique()->url;
        $contributor->html_url = $this->faker()->unique()->url;

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
