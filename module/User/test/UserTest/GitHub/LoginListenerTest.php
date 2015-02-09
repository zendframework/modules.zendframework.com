<?php

namespace UserTest\GitHub;

use Hybrid_User_Profile;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use User\Entity\User;
use User\GitHub\LoginListener;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManager;

/**
 * Test case for {@see \User\GitHub\LoginListener}
 */
class LoginListenerTest extends PHPUnit_Framework_TestCase
{
    /** @var LoginListener */
    protected $listener;

    protected function setUp()
    {
        $this->listener = new LoginListener();
    }

    /**
     * @covers \User\GitHub\LoginListener::attachShared
     */
    public function testAttach()
    {
        $sharedEventManager = new SharedEventManager();

        $this->listener->attachShared($sharedEventManager);

        $listeners = $sharedEventManager->getListeners(
            'ScnSocialAuth\Authentication\Adapter\HybridAuth',
            'registerViaProvider'
        );

        $this->assertFalse($listeners->isEmpty());
    }

    /**
     * @covers \User\GitHub\LoginListener::onRegister
     */
    public function testOnRegisterWithValidEvent()
    {
        $user = new User();
        $profile = new Hybrid_User_Profile();
        $photoUrl = 'http://placehold.it/50x50';
        $profile->photoURL = $photoUrl;
        $profile->profileURL = 'https://github.com/username';

        $event = new Event(null, null, [
            'user'        => $user,
            'userProfile' => $profile,
            'provider'    => 'github',
        ]);

        $this->listener->onRegister($event);

        $this->assertSame('username', $user->getUsername());
        $this->assertSame($photoUrl, $user->getPhotoUrl());
    }

    /**
     * @covers \User\GitHub\LoginListener::onRegister
     */
    public function testOnRegisterWithInvalidEvent()
    {
        $user = new User();
        $profile = new Hybrid_User_Profile();
        $event = new Event(null, null, [
            'user'        => $user,
            'userProfile' => $profile,
        ]);

        $this->listener->onRegister($event);

        $this->assertEquals($user, $user);
    }

    /**
     * @dataProvider getEvents
     * @covers \User\GitHub\LoginListener::isEventValid
     */
    public function testIsEventValid($event, $expectedValidity)
    {
        $class = new ReflectionClass($this->listener);
        $method = $class->getMethod('isEventValid');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->listener, [$event]);
        $this->assertEquals($expectedValidity, $result);
    }

    /**
     * @covers \User\GitHub\LoginListener::updateLocalUser
     */
    public function testUpdateLocalUser()
    {
        $user = new User();
        $profile = new Hybrid_User_Profile();
        $photoUrl = 'http://placehold.it/50x50';
        $profile->photoURL = $photoUrl;
        $profile->profileURL = 'https://github.com/username';

        $class = new ReflectionClass($this->listener);
        $method = $class->getMethod('updateLocalUser');
        $method->setAccessible(true);
        $result =  $method->invokeArgs($this->listener, [$user, $profile]);

        $this->assertSame('username', $result->getUsername());
        $this->assertSame($photoUrl, $result->getPhotoUrl());
    }

    /**
     * @covers \User\GitHub\LoginListener::getUsernameFromProfileUrl
     */
    public function testGetUsernameFromProfileUrl()
    {
        $class = new ReflectionClass($this->listener);
        $method = $class->getMethod('getUsernameFromProfileUrl');
        $method->setAccessible(true);
        $result =  $method->invokeArgs($this->listener, ['https://github.com/username']);

        $this->assertSame('username', $result);
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        $user = new User();
        $profile = new Hybrid_User_Profile();
        $photoUrl = 'http://placehold.it/50x50';
        $profile->photoURL = $photoUrl;
        $profile->profileURL = 'https://github.com/username';

        return [
            [
                new Event(null, null, [
                    'user'        => $user,
                    'userProfile' => $profile,
                    'provider'    => 'github',
                ]),
                true, // expected event validity for LoginListener
            ],
            [
                new Event(null, null, [
                    'user'        => null,
                    'userProfile' => $profile,
                    'provider'    => 'github',
                ]),
                false,
            ],
            [
                new Event(null, null, [
                    'user'        => $user,
                    'userProfile' => null,
                    'provider'    => 'github',
                ]),
                false,
            ],
            [
                new Event(null, null, [
                    'user'        => $user,
                    'userProfile' => $profile,
                    'provider'    => 'invalid',
                ]),
                false,
            ],
            [
                new Event(null, null, [
                    'user'        => $user,
                    'userProfile' => $profile,
                ]),
                false,
            ],
        ];
    }
}
