<?php

namespace UserTest\Entity;

use PHPUnit_Framework_TestCase;
use User\Entity;
use ZfcUser\Entity\User;

class UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Entity\User
     */
    private $user;

    public function setUp()
    {
        $this->user = new Entity\User();
    }

    public function testInstanceOfZfcUser()
    {
        $this->assertInstanceOf(User::class, $this->user);
    }

    public function testDefaults()
    {
        $this->assertNull($this->user->getCreatedAt());
        $this->assertNull($this->user->getPhotoUrl());
    }

    public function testFluentInterface()
    {
        $this->assertSame($this->user, $this->user->setCreatedAt('2013-02-28 13:05:00'));
        $this->assertSame($this->user, $this->user->setPhotoUrl('http://www.example.com/photo.jpg'));
    }

    public function testSetCreatedAt()
    {
        $createdAt = '2013-02-28 13:05:00';

        $this->user->setCreatedAt($createdAt);

        $this->assertSame($createdAt, $this->user->getCreatedAt());
    }

    public function testSetPhotoUrl()
    {
        $photoUrl = 'http://www.example.com/photo.jpg';

        $this->user->setPhotoUrl($photoUrl);

        $this->assertSame($photoUrl, $this->user->getPhotoUrl());
    }
}
