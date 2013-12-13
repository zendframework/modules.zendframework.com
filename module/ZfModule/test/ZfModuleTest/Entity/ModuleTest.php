<?php

namespace ZfModuleTest\Entity;

use ZfModule\Entity\Module;
use PHPUnit_Framework_TestCase;

class ModuleTest extends PHPUnit_Framework_TestCase
{
    protected $module;

    public function setUp()
    {
        $this->module = new Module;
    }

    public function testModuleConstruction()
    {
        $this->assertInstanceOf('ZfModule\Entity\Module', $this->module);
    }

    public function testSetOwner()
    {
        $this->module->setOwner('johndoe');

        $this->assertEquals('johndoe', $this->module->getOwner());
    }

    public function testSetId()
    {
        $this->module->setId(99);

        $this->assertEquals(99, $this->module->getId());
    }

    public function testSetUrl()
    {
        $this->module->setUrl('http://example.com');

        $this->assertEquals('http://example.com', $this->module->getUrl());
    }

    public function testSetName()
    {
        $this->module->setName('Super Great Happy Good Time Module');

        $this->assertEquals('Super Great Happy Good Time Module', $this->module->getName());
    }

    public function testSetDescription()
    {
        $this->module->setDescription('Lorem ipsum dolor sit amet');

        $this->assertEquals('Lorem ipsum dolor sit amet', $this->module->getDescription());
    }

    public function testSetCreatedAt()
    {
        $this->module->setCreatedAt('2013-02-28 13:05:00');

        $this->assertEquals('2013-02-28 13:05:00', $this->module->getCreatedAt());
    }

    public function testSetUpdatedAt()
    {
        $this->module->setUpdatedAt('2013-02-28 13:05:00');

        $this->assertEquals('2013-02-28 13:05:00', $this->module->getUpdatedAt());
    }


    public function testSetPhotoUrl()
    {
        $this->module->setPhotoUrl('http://www.example.com/photo.jpg');

        $this->assertEquals('http://www.example.com/photo.jpg', $this->module->getPhotoUrl());
    }


}
