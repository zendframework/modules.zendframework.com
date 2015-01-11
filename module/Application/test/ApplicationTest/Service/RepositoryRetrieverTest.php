<?php

namespace ApplicationTest\Service;

use Application\Service\RepositoryRetriever;
use EdpGithub\Api;
use EdpGithub\Collection;
use PHPUnit_Framework_TestCase;

class RepositoryRetrieverTest extends PHPUnit_Framework_TestCase
{
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
            'url' => 'http://foo.com',
        ];

        $client = $this->getClientMock(
            new Api\Repos(),
            $payload
        );

        $service = new RepositoryRetriever($client);

        $metadata = $service->getUserRepositoryMetadata('foo', 'bar');

        $this->assertInstanceOf('stdClass', $metadata);
        $this->assertEquals($payload, (array)$metadata);
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

    public function testResponseContentMissingOnGetRepositoryFileContent()
    {
        $payload = [];

        $client = $this->getClientMock(
            new Api\Repos(),
            $payload
        );

        $service = new RepositoryRetriever($client);

        $response = $service->getRepositoryFileContent('foo', 'bar', 'baz');

        $this->assertNull($response);
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
}
