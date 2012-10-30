<?php

namespace ApplicationTest\Service;

use PHPUnit_Framework_TestCase;
use Application\Service\Repository;

class RepositoryTest extends PHPUnit_Framework_TestCase
{

    public function testGetAllRepositories()
    {
        $repo = $this->getMock(
            'EdpGithub\Collection\RepositoryCollection',
            array(),
            array($this->getMock('EdpGithub\Http\Client'), 'somePath')
        );

        $currentUser = $this->getMock('EdpGithub\Api\CurrentUser');
        $currentUser->expects($this->once())
            ->method('repos')
            ->will($this->returnValue($repo));

        $api = $this->getMock('EdpGithub\Client');

        $api->expects($this->once())
            ->method('api')
            ->with($this->equalTo('current_user'))
            ->will($this->returnValue($currentUser));


        $service = new Repository();
        $service->setApi($api);

        $result = $service->getAllRepository('member');

        $this->assertInstanceOf('edpGithub\Collection\RepositoryCollection', $result);
    }
}