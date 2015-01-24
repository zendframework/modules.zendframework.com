<?php

namespace UserTest\Integration\GitHub;

use ApplicationTest\Integration\Util\Bootstrap;
use Hybrid_User_Profile;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ScnSocialAuth\Authentication\Adapter\HybridAuth;
use User\GitHub\LoginListener;
use User\Module;
use Zend\EventManager\EventManager;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Application;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\CallbackHandler;

/**
 * Integration test case for {@see \User\GitHub\LoginListener}
 *
 * @coversNothing
 */
class LoginListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var LoginListener
     */
    private $loginListener;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->loginListener = $this->serviceManager->get(LoginListener::class);
    }

    public function testEventListenerIsAttached()
    {
        /* @var Application $application */
        $application = $this->serviceManager->get('Application');

        /* @var ModuleManager $moduleManager */
        $moduleManager = $this->serviceManager->get('ModuleManager');

        /* @var Module $userModule */
        $userModule = $moduleManager->loadModule('User');
        $userModule->bootstrap($moduleManager, $application);

        /* @var EventManager $eventManager */
        $eventManager  = $this->serviceManager->get('EventManager');

        $listeners = $eventManager->getSharedManager()->getListeners(
            'ScnSocialAuth\Authentication\Adapter\HybridAuth',
            'registerViaProvider'
        );

        $this->assertFalse($listeners->isEmpty());

        $attached = false;
        $expectedCallback = [$this->loginListener, 'onRegister'];

        /* @var CallbackHandler $listener */
        foreach ($listeners as $listener) {
            if ($listener->getCallback() === $expectedCallback) {
                $attached = true;
                break;
            }
        }

        $this->assertTrue($attached, '"User\GitHub\LoginListener::onRegister" is not attached');
    }

    /**
     * Verifies that `registerViaProvider` event is triggered during registration process with GitHub account
     * in ScnSocialAuth module as prevention against regression.
     *
     * @link https://github.com/zendframework/modules.zendframework.com/pull/286
     */
    public function testEventIsTriggered()
    {
        $triggered = false;

        $eventManager = new EventManager();
        $eventManager->attach('registerViaProvider', function () use (&$triggered) {
            $triggered = true;
        });

        $mapper = $this->getMockBuilder('ZfcUser\Mapper\UserInterface')->getMockForAbstractClass();

        $hybridAuth = new HybridAuth();
        $hybridAuth->setEventManager($eventManager);
        $hybridAuth->setServiceManager($this->serviceManager);
        $hybridAuth->setZfcUserMapper($mapper);

        $class = new ReflectionClass($hybridAuth);
        $method = $class->getMethod('githubToLocalUser');
        $method->setAccessible(true);
        $method->invokeArgs($hybridAuth, [new Hybrid_User_Profile()]);

        $this->assertTrue($triggered);
    }
}
