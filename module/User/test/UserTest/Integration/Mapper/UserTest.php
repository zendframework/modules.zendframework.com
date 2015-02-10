<?php

namespace UserTest\Integration\Mapper;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;
use User\Mapper;

/**
 * @coversNothing
 */
class UserTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateService()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->assertInstanceOf(
            Mapper\User::class,
            $serviceManager->get('zfcuser_user_mapper')
        );
    }
}
