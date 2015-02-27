<?php

namespace ZfModuleTest\Mapper;

class ModuleToFeedTest extends \PHPUnit_Framework_TestCase
{
    public function testAddModule()
    {
        $dateTime = new \DateTime();

        $module = $this->getMockBuilder(\ZfModule\Entity\Module::class)->getMock();
        $module->expects($this->once())->method('getName')->willReturn('name');
        $module->expects($this->once())->method('getDescription')->willReturn('description');
        $module->expects($this->once())->method('getCreatedAtDateTime')->willReturn($dateTime);

        $entry = $this->getMockBuilder(\Zend\Feed\Writer\Entry::class)->disableOriginalConstructor()->getMock();
        $entry->expects($this->once())->method('setTitle')->with('name');
        $entry->expects($this->once())->method('setDescription')->with('description');
        $entry->expects($this->once())->method('setLink')->with('url');
        $entry->expects($this->once())->method('setDateCreated')->with($dateTime);

        $url = $this->getMockBuilder(\Zend\Mvc\Controller\Plugin\Url::class)->getMock();
        $url->expects($this->once())->method('fromRoute')->willReturn('url');

        $feed = $this->getMockBuilder(\Zend\Feed\Writer\Feed::class)->disableOriginalConstructor()->getMock();
        $feed->expects($this->once())->method('createEntry')->willReturn($entry);
        $feed->expects($this->once())->method('addEntry')->with($entry);

        $moduleToFeed = new \ZfModule\Mapper\ModuleToFeed($feed, $url);
        $moduleToFeed->addModule($module);
    }

    public function testAddModuleWithoutDesctription()
    {
        $dateTime = new \DateTime();

        $module = $this->getMockBuilder(\ZfModule\Entity\Module::class)->getMock();
        $module->expects($this->once())->method('getName')->willReturn('name');
        $module->expects($this->once())->method('getDescription')->willReturn(null);
        $module->expects($this->once())->method('getCreatedAtDateTime')->willReturn($dateTime);

        $entry = $this->getMockBuilder(\Zend\Feed\Writer\Entry::class)->disableOriginalConstructor()->getMock();
        $entry->expects($this->once())->method('setTitle')->with('name');
        $entry->expects($this->once())->method('setDescription')->with('No description available');
        $entry->expects($this->once())->method('setLink')->with('url');
        $entry->expects($this->once())->method('setDateCreated')->with($dateTime);

        $url = $this->getMockBuilder(\Zend\Mvc\Controller\Plugin\Url::class)->getMock();
        $url->expects($this->once())->method('fromRoute')->willReturn('url');

        $feed = $this->getMockBuilder(\Zend\Feed\Writer\Feed::class)->disableOriginalConstructor()->getMock();
        $feed->expects($this->once())->method('createEntry')->willReturn($entry);
        $feed->expects($this->once())->method('addEntry')->with($entry);

        $moduleToFeed = new \ZfModule\Mapper\ModuleToFeed($feed, $url);
        $moduleToFeed->addModule($module);
    }

    public function testAddMultipleModules()
    {
        $modulesCount = 11;
        $module = $this->getMockBuilder(\ZfModule\Entity\Module::class)->getMock();
        $modules = array_fill(0, $modulesCount, $module);

        $moduleToFeed = $this->getMockBuilder(\ZfModule\Mapper\ModuleToFeed::class)->setMethods(['addModule'])->disableOriginalConstructor()->getMock();
        $moduleToFeed->expects($this->exactly($modulesCount))->method('addModule')->with($module);
        $moduleToFeed->addModules($modules);
    }
}
