<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZfModule\Mapper;

use Zend\Feed\Writer\Feed;
use ZfModule\Entity\Module as ModuleEntity;

/**
 * ModuleToFeed
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class ModuleToFeed
{
    /**
     * @var Feed
     */
    protected $feed;

    /**
     * @param Feed $feed
     */
    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
    }

    /**
     * @param array $modules
     */
    public function addModules(array $modules)
    {
        foreach ($modules as $module) {
            $this->addModule($module);
        }
    }

    /**
     * @param ModuleEntity $module
     * @return \Zend\Feed\Writer\Entry
     */
    public function addModule(ModuleEntity $module)
    {
        $entry = $this->feed->createEntry();
        $entry->setTitle($module->getName());

        if (empty($module->getDescription())) {
            $moduleDescription = 'No description available';
        } else {
            $moduleDescription = $module->getDescription();
        }

        $entry->setDescription($moduleDescription);
        $entry->setLink($module->getUrl());
        $entry->setDateCreated($module->getCreatedAtDateTime());

        $this->feed->addEntry($entry);

        return $entry;
    }
}
