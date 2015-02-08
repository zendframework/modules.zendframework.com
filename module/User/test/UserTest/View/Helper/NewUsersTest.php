<?php

namespace UserTest\View\Helper;

use PHPUnit_Framework_TestCase;
use User\Mapper;
use User\View\Helper;
use Zend\View;

class NewUsersTest extends PHPUnit_Framework_TestCase
{
    public function testInvokeRendersViewScriptWith16MostRecentUsers()
    {
        $users = [
            'Jane',
            'Suzie',
            'Jack',
        ];

        $userMapper = $this->getMockBuilder(Mapper\User::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $userMapper
            ->expects($this->once())
            ->method('findAll')
            ->with(
                $this->equalTo(16),
                $this->equalTo('created_at'),
                $this->equalTo('DESC')
            )
            ->willReturn($users)
        ;

        $view = $this->getMockBuilder(View\Renderer\RendererInterface::class)->getMock();

        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('user/helper/new-users'),
                $this->equalTo([
                    'users' => $users,
                ])
            )
        ;

        $helper = new Helper\NewUsers($userMapper);
        $helper->setView($view);

        $helper();
    }
}
