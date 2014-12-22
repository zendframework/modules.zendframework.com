<?php

namespace ApplicationTest\Service;

use Application\Service\RepositoryRetriever;
use EdpGithub\Api;
use EdpGithub\Listener\Exception\RuntimeException;
use PHPUnit_Framework_TestCase;

class RepositoryRetrieverTest extends PHPUnit_Framework_TestCase
{
    private $response;
    private $headers;
    private $httpClient;
    private $client;

    protected function setUp()
    {
        $this->response = $this->getMock('Zend\Http\Response');
        $this->headers = $this->getMock('Zend\Http\Headers');
        $this->httpClient = $this->getMock('EdpGithub\Http\Client');
        $this->client = $this->getMock('EdpGithub\Client');
    }

    protected function tearDown()
    {
        $this->response = null;
        $this->headers = null;
        $this->httpClient = null;
        $this->client = null;
    }

    private function getClientMock(Api\AbstractApi $apiInstance, $result)
    {
        $this->response->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($result));

        $this->response->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue($this->headers));

        $this->httpClient->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->response));

        $this->client->expects($this->any())
            ->method('getHttpClient')
            ->will($this->returnValue($this->httpClient));

        $apiInstance->setClient($this->client);

        $this->client->expects($this->any())
            ->method('api')
            ->will($this->returnValue($apiInstance));

        return $this->client;
    }

    private function prepareService(Api\AbstractApi $apiInstance, $result)
    {
        return new RepositoryRetriever(
            $this->getClientMock($apiInstance, $result)
        );
    }

    public function testCanRetrieveUserRepositories()
    {
        $payload = [
            ['name' => 'foo'],
            ['name' => 'bar'],
            ['name' => 'baz']
        ];

        $service = $this->prepareService(new Api\User, json_encode($payload));

        $repositories = $service->getUserRepositories('foo');
        $this->assertInstanceOf('EdpGithub\Collection\RepositoryCollection', $repositories);

        $count = 0;
        foreach ($repositories as $repository) {
            $this->assertEquals(current($payload), (array)$repository);
            next($payload);
            ++$count;
        }

        $this->assertEquals(count($payload), $count);
    }

    public function testCanRetrieveUserRepositoryMetadata()
    {
        $payload = [
            'name' => 'foo',
            'url' => 'http://foo.com'
        ];

        $service = $this->prepareService(new Api\Repos, json_encode($payload));
        $metadata = $service->getUserRepositoryMetadata('foo', 'bar');

        $this->assertInstanceOf('stdClass', $metadata);
        $this->assertEquals($payload, (array)$metadata);
    }

    public function testCanRetrieveRepositoryFileContent()
    {
        $payload = [
            'content' => base64_encode('foo')
        ];
        $service = $this->prepareService(new Api\Repos, json_encode($payload));
        $response = $service->getRepositoryFileContent('foo', 'bar', 'foo.baz');

        $this->assertEquals('foo', $response);
    }

    public function testResponseContentMissingOnGetRepositoryFileContent()
    {
        $payload = [];
        $service = $this->prepareService(new Api\Repos, json_encode($payload));
        $response = $service->getRepositoryFileContent('foo', 'bar', 'baz');

        $this->assertNull($response);
    }

    public function testCanRetrieveRepositoryFileMetadata()
    {
        $payload = [
            'name' => 'foo',
            'url' => 'http://foo.com'
        ];

        $service = $this->prepareService(new Api\Repos, json_encode($payload));
        $metadata = $service->getRepositoryFileMetadata('foo', 'bar', 'baz');

        $this->assertInstanceOf('stdClass', $metadata);
        $this->assertEquals($payload, (array)$metadata);
    }

    public function testCanRetrieveAuthenticatedUserRepositories()
    {
        $payload = [
            ['name' => 'foo'],
            ['name' => 'bar'],
            ['name' => 'baz']
        ];

        $service = $this->prepareService(new Api\CurrentUser, json_encode($payload));

        $repositories = $service->getAuthenticatedUserRepositories();
        $this->assertInstanceOf('EdpGithub\Collection\RepositoryCollection', $repositories);

        $count = 0;
        foreach ($repositories as $repository) {
            $this->assertEquals(current($payload), (array)$repository);
            next($payload);
            ++$count;
        }

        $this->assertEquals(count($payload), $count);
    }
}
