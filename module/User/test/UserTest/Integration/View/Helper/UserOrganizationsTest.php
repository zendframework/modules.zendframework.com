<?php

namespace UserTest\Integration\View\Helper;

class UserOrganizationsTest extends AbstractTestCase
{
    public function testCanCreateService()
    {
        $this->assertInstanceOf(
            'User\View\Helper\UserOrganizations',
            $this->getHelperPluginManager()->get('userOrganizations')
        );
    }
}
