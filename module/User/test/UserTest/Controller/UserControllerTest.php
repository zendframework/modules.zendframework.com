<?php

namespace UserTest\Controller;

use ApplicationTest\Integration\Util\Bootstrap;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use ZfcUser\Controller\UserController;

/**
 * @covers UserController
 */
class UserControllerTest extends AbstractHttpControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(Bootstrap::getConfig());
    }

    public function testCallableConstructorArgumentCanBeSet()
    {
        $callable = function () {
        };

        try {
            $userController = new UserController($callable);
            $this->assertInstanceOf(UserController::class, $userController);
        } catch (\Exception $e) {
            $this->fail('Constructor fails with Callable Argument');
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage You must supply a callable redirectCallback
     */
    public function testInvalidConstructorArgumentThrowsError()
    {
        new UserController('notCallable');
    }
}
