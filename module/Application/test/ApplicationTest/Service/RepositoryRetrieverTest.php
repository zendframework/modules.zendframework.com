<?php

namespace ApplicationTest\Service;

use Application\Service\RepositoryRetriever;
use EdpGithub\Api;
use EdpGithub\Listener\Exception\RuntimeException;
use PHPUnit_Framework_TestCase;

class RepositoryRetrieverTest extends PHPUnit_Framework_TestCase
{
    public $response;
    public $headers;
    public $httpClient;
    public $client;

    public function setUp()
    {
        $this->response = $this->getMock('Zend\Http\Response');
        $this->headers = $this->getMock('Zend\Http\Headers');
        $this->httpClient = $this->getMock('EdpGithub\Http\Client');
        $this->client = $this->getMock('EdpGithub\Client');
    }

    public function getClientMock(Api\AbstractApi $apiInstance, $result)
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

    public function getRepositoryRetrieverInstance(Api\AbstractApi $apiInstance, $result)
    {
        $clientMock = $this->getClientMock($apiInstance, $result);
        return new RepositoryRetriever($clientMock);
    }

    public function testCanRetrieveUserRepositories()
    {
        $payload = [
            ['name' => 'foo'],
            ['name' => 'bar'],
            ['name' => 'baz']
        ];

        $instance = $this->getRepositoryRetrieverInstance(new Api\User, json_encode($payload));

        $repositories = $instance->getUserRepositories('foo');
        $this->assertInstanceOf('Generator', $repositories);

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

        $instance = $this->getRepositoryRetrieverInstance(new Api\Repos, json_encode($payload));
        $metadata = $instance->getUserRepositoryMetadata('foo', 'bar');

        $this->assertInstanceOf('stdClass', $metadata);
        $this->assertEquals($payload, (array)$metadata);
    }

    public function testErrorOnRetreiveUserRepositoryMetadata()
    {
        $this->client->expects($this->once())
            ->method('api')
            ->willThrowException(new RuntimeException);

        $instance = new RepositoryRetriever($this->client);
        $response = $instance->getUserRepositoryMetadata('foo', 'bar');
        $this->assertFalse($response);
    }

    public function testCanRetrieveRepositoryFileContent()
    {
        $payload = [
            'content' => base64_encode('foo')
        ];
        $instance = $this->getRepositoryRetrieverInstance(new Api\Repos, json_encode($payload));
        $response = $instance->getRepositoryFileContent('foo', 'bar', 'foo.baz');

        $this->assertEquals('foo', $response);
    }

    public function testResponseContentMissingOnGetRepositoryFileContent()
    {
        $payload = [];
        $instance = $this->getRepositoryRetrieverInstance(new Api\Repos, json_encode($payload));
        $response = $instance->getRepositoryFileContent('foo', 'bar', 'baz');

        $this->assertFalse($response);
    }

    public function testCanRetrieveRepositoryFileMetadata()
    {
        $payload = [
            'name' => 'foo',
            'url' => 'http://foo.com'
        ];

        $instance = $this->getRepositoryRetrieverInstance(new Api\Repos, json_encode($payload));
        $metadata = $instance->getRepositoryFileMetadata('foo', 'bar', 'baz');

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

        $instance = $this->getRepositoryRetrieverInstance(new Api\CurrentUser, json_encode($payload));

        $repositories = $instance->getAuthenticatedUserRepositories();
        $this->assertInstanceOf('Generator', $repositories);

        $count = 0;
        foreach ($repositories as $repository) {
            $this->assertEquals(current($payload), (array)$repository);
            next($payload);
            ++$count;
        }

        $this->assertEquals(count($payload), $count);
    }
}
