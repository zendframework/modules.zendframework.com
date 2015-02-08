<?php

namespace UserTest\View\Helper;

use EdpGithub\Api;
use EdpGithub\Client;
use PHPUnit_Framework_TestCase;
use User\View\Helper;
use Zend\View;

class UserOrganizationsTest extends PHPUnit_Framework_TestCase
{
    public function testInvokeRendersViewScriptWithUserOrganizations()
    {
        $organizations = [
            'foo',
            'bar',
            'baz',
        ];

        $currentUserApi = $this->getMockBuilder(Api\CurrentUser::class)->getMock();

        $currentUserApi
            ->expects($this->once())
            ->method('orgs')
            ->willReturn($organizations)
        ;

        $githubClient = $this->getMockBuilder(Client::class)->getMock();

        $githubClient
            ->expects($this->once())
            ->method('api')
            ->with($this->equalTo('current_user'))
            ->willReturn($currentUserApi)
        ;

        $view = $this->getMockBuilder(View\Renderer\RendererInterface::class)->getMock();

        $view
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('user/helper/user-organizations.phtml'),
                $this->equalTo([
                    'orgs' => $organizations,
                ])
            )
        ;

        $helper = new Helper\UserOrganizations($githubClient);
        $helper->setView($view);

        $helper();
    }
}
