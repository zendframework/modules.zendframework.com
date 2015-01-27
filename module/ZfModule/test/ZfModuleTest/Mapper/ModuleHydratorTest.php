<?php

namespace ZfModuleTest\Mapper;

use ZfModule\Entity\Module;
use PHPUnit_Framework_TestCase;
use ZfModule\Mapper\ModuleHydrator;

class ModuleHydratorTest extends PHPUnit_Framework_TestCase
{
    public function testEntityHydratorCanExtract()
    {
        $moduleEntity = new Module();
        $moduleEntity->setCreatedAt('2013-02-28 13:05:00');
        $moduleEntity->setUpdatedAt('2014-05-10 06:48:15');
        $moduleEntity->setDescription('fooDescription');
        $moduleEntity->setId(999);
        $moduleEntity->setName('fooName');
        $moduleEntity->setOwner('fooOwner');
        $moduleEntity->setPhotoUrl('fooPhotoUrl');
        $moduleEntity->setUrl('fooUrl');

        $hydrator = new ModuleHydrator();
        $extractedModuleArray = $hydrator->extract($moduleEntity);

        $this->assertEquals([
            'name' => 'fooName',
            'description' => 'fooDescription',
            'url' => 'fooUrl',
            'owner' => 'fooOwner',
            'module_id' => 999,
            'created_at' => '2013-02-28 13:05:00',
            'updated_at' => '2014-05-10 06:48:15',
            'photo_url' => 'fooPhotoUrl'
        ], $extractedModuleArray);
    }

    public function testEntityHydratorCanHydrate()
    {
        $moduleData = [
            'name' => 'fooName',
            'description' => 'fooDescription',
            'url' => 'fooUrl',
            'owner' => 'fooOwner',
            'module_id' => 999,
            'created_at' => '2013-02-28 13:05:00',
            'updated_at' => '2014-05-10 06:48:15',
            'photo_url' => 'fooPhotoUrl'
        ];

        $hydrator = new ModuleHydrator();
        $hydratedModule = $hydrator->hydrate($moduleData, new Module);

        $moduleEntity = new Module;
        $moduleEntity->setCreatedAt('2013-02-28 13:05:00');
        $moduleEntity->setUpdatedAt('2014-05-10 06:48:15');
        $moduleEntity->setDescription('fooDescription');
        $moduleEntity->setId(999);
        $moduleEntity->setName('fooName');
        $moduleEntity->setOwner('fooOwner');
        $moduleEntity->setPhotoUrl('fooPhotoUrl');
        $moduleEntity->setUrl('fooUrl');

        $this->assertEquals($moduleEntity, $hydratedModule);
    }
}