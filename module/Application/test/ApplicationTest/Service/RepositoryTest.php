<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use Application\Service\Repository;

class RepositoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // your code here
    }

    public function tearDown()
    {
        // your code here
    }

    public function testGetAllRepositories()
    {
        $api = $this->getMock('EdpGithub\ApiClient\ApiClient');
        $repo =  $this->getMock('EdpGithub\ApiClient\ApiFactory\Service\Repo');

        $api->expects($this->once())
            ->method('getService')
            ->with($this->equalTo('Repo'))
            ->will($this->returnValue('test'));

        $service = new Repository();
        $service->setApi($api);

        $service->getAllRepository('member');


    }
}