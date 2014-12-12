<?php

namespace UserTest\Integration\View\Helper;

class UserRepositoriesTest extends AbstractTestCase
{
    public function testCanCreateService()
    {
        $this->assertInstanceOf(
            'User\View\Helper\UserRepositories',
            $this->getHelperPluginManager()->get('userRepositories')
        );
    }
}
