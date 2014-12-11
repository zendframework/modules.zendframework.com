<?php

namespace UserTest\Integration\View\Helper;

class NewUsersTest extends AbstractTestCase
{
    public function testCanCreateService()
    {
        $this->assertInstanceOf(
            'User\View\Helper\NewUsers',
            $this->getHelperPluginManager()->get('newUsers')
        );
    }
}
