<?php

namespace ZfModuleTest\Entity;

use DateTime;
use PHPUnit_Framework_TestCase;
use ZfModule\Entity;

class ModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Entity\Module
     */
    private $module;

    public function setUp()
    {
        $this->module = new Entity\Module();
    }

    public function testDefaults()
    {
        $this->assertNull($this->module->getId());
        $this->assertNull($this->module->getName());
        $this->assertNull($this->module->getDescription());
        $this->assertNull($this->module->getOwner());
        $this->assertNull($this->module->getPhotoUrl());
        $this->assertNull($this->module->getUrl());
        $this->assertEquals(new DateTime(), $this->module->getCreatedAtDateTime());
        $this->assertNull($this->module->getCreatedAt());
        $this->assertNull($this->module->getUpdatedAt());
        $this->assertNull($this->module->getIdentifier());
    }

    public function testSetOwner()
    {
        $owner = 'johndoe';

        $this->module->setOwner($owner);

        $this->assertSame($owner, $this->module->getOwner());
    }

    public function testSetId()
    {
        $id = 99;

        $this->module->setId($id);

        $this->assertSame($id, $this->module->getId());
    }

    public function testSetUrl()
    {
        $url = 'http://example.com';

        $this->module->setUrl($url);

        $this->assertSame($url, $this->module->getUrl());
    }

    public function testSetName()
    {
        $name = 'Super Great Happy Good Time Module';

        $this->module->setName($name);

        $this->assertSame($name, $this->module->getName());
    }

    public function testSetDescription()
    {
        $description = 'Lorem ipsum dolor sit amet';

        $this->module->setDescription($description);

        $this->assertSame($description, $this->module->getDescription());
    }

    public function testSetCreatedAt()
    {
        $createdAt = '2013-02-28 13:05:00';

        $this->module->setCreatedAt($createdAt);

        $this->assertSame($createdAt, $this->module->getCreatedAt());
        $this->assertEquals(new DateTime($createdAt), $this->module->getCreatedAtDateTime());
    }

    public function testSetUpdatedAt()
    {
        $updatedAt = '2013-02-28 13:05:00';

        $this->module->setUpdatedAt($updatedAt);

        $this->assertSame($updatedAt, $this->module->getUpdatedAt());
    }

    public function testSetPhotoUrl()
    {
        $photoUrl = 'http://www.example.com/photo.jpg';

        $this->module->setPhotoUrl($photoUrl);

        $this->assertSame($photoUrl, $this->module->getPhotoUrl());
    }

    public function testGetIdentifier()
    {
        $this->module->setOwner('owner');
        $this->module->setName('name');

        $this->assertEquals('owner/name', $this->module->getIdentifier());
    }

    public function testGetNullIdentifierOnlyOwner()
    {
        $this->module->setOwner('owner');

        $this->assertNull($this->module->getIdentifier());
    }

    public function testGetNullIdentifierOnlyName()
    {
        $this->module->setName('name');

        $this->assertNull($this->module->getIdentifier());
    }
}
