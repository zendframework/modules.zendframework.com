<?php

namespace ZfModuleTest\Mapper;

use PHPUnit_Framework_TestCase;
use stdClass;
use ZfModule\Entity\Module;
use ZfModule\Mapper\ModuleHydrator;

class ModuleHydratorTest extends PHPUnit_Framework_TestCase
{
    public function testEntityHydratorCanExtract()
    {
        $module = new Module();
        $module->setCreatedAt('2013-02-28 13:05:00');
        $module->setUpdatedAt('2014-05-10 06:48:15');
        $module->setDescription('fooDescription');
        $module->setId(999);
        $module->setName('fooName');
        $module->setOwner('fooOwner');
        $module->setPhotoUrl('fooPhotoUrl');
        $module->setUrl('fooUrl');

        $data = [
            'name' => 'fooName',
            'description' => 'fooDescription',
            'url' => 'fooUrl',
            'owner' => 'fooOwner',
            'module_id' => 999,
            'created_at' => '2013-02-28 13:05:00',
            'updated_at' => '2014-05-10 06:48:15',
            'photo_url' => 'fooPhotoUrl',
        ];

        $hydrator = new ModuleHydrator();

        $this->assertSame($data, $hydrator->extract($module));
    }

    /**
     * @expectedException \ZfModule\Mapper\Exception\InvalidArgumentException
     * @expectedExceptionMessage $object must be an instance of ZfModule\Entity\ModuleEntityInterface
     */
    public function testEntityHydratorRefusesToExtractInvalidObject()
    {
        $hydrator = new ModuleHydrator();
        $hydrator->extract(new stdClass());
    }

    public function testEntityHydratorCanHydrate()
    {
        $data = [
            'name' => 'fooName',
            'description' => 'fooDescription',
            'url' => 'fooUrl',
            'owner' => 'fooOwner',
            'module_id' => 999,
            'created_at' => '2013-02-28 13:05:00',
            'updated_at' => '2014-05-10 06:48:15',
            'photo_url' => 'fooPhotoUrl',
        ];

        $module = new Module();
        $module->setCreatedAt('2013-02-28 13:05:00');
        $module->setUpdatedAt('2014-05-10 06:48:15');
        $module->setDescription('fooDescription');
        $module->setId(999);
        $module->setName('fooName');
        $module->setOwner('fooOwner');
        $module->setPhotoUrl('fooPhotoUrl');
        $module->setUrl('fooUrl');

        $hydrator = new ModuleHydrator();

        $this->assertEquals($module, $hydrator->hydrate($data, new Module()));
    }

    /**
     * @expectedException \ZfModule\Mapper\Exception\InvalidArgumentException
     * @expectedExceptionMessage $object must be an instance of ZfModule\Entity\ModuleEntityInterface
     */
    public function testEntityHydratorRefusesToHydrateInvalidObject()
    {
        $hydrator = new ModuleHydrator();
        $hydrator->hydrate([], new stdClass());
    }
}
