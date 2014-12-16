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
    /** @var Application */
    protected $application;

    /** @var ModuleManager */
    protected $moduleManager;

    /** @var LoginListener */
    protected $loginListener;

    /** @var EventManager */
    protected $eventManager;

    protected function setUp()
    {
        $serviceManager      = $this->getServiceManager();
        $this->application   = $serviceManager->get('Application');
        $this->moduleManager = $serviceManager->get('ModuleManager');
        $this->loginListener = $serviceManager->get('User\GitHub\LoginListener');
        $this->eventManager  = $serviceManager->get('EventManager');
    }

    /**
     * @return ServiceManager
     */
    protected function getServiceManager()
    {
        return Bootstrap::getServiceManager();
    }

    public function testEventListenerIsAttached()
    {
        /** @var Module $userModule */
        $userModule = $this->moduleManager->loadModule('User');
        $userModule->bootstrap($this->moduleManager, $this->application);

        $listeners = $this->eventManager->getSharedManager()
            ->getListeners('ScnSocialAuth\Authentication\Adapter\HybridAuth', 'registerViaProvider');

        $this->assertFalse($listeners->isEmpty());

        $attached = false;
        $expectedCallback = [$this->loginListener, 'onRegister'];
        /** @var CallbackHandler $listener */
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
        $serviceManager = $this->getServiceManager();
        $eventManager = $this->getMockBuilder('Zend\EventManager\EventManagerInterface')->getMockForAbstractClass();
        $eventManager->expects($this->atLeastOnce())
            ->method('trigger')
            ->will($this->returnValueMap([
                ['registerViaProvider', null, null, null],
            ]));
        $mapper = $this->getMockBuilder('ZfcUser\Mapper\UserInterface')->getMockForAbstractClass();

        $hybridAuth = new HybridAuth();
        $hybridAuth->setEventManager($eventManager);
        $hybridAuth->setServiceManager($serviceManager);
        $hybridAuth->setZfcUserMapper($mapper);

        $class = new ReflectionClass($hybridAuth);
        $method = $class->getMethod('githubToLocalUser');
        $method->setAccessible(true);
        $method->invokeArgs($hybridAuth, [new Hybrid_User_Profile()]);
    }
}
