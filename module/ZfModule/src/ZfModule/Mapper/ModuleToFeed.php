<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZfModule\Mapper;

use Zend\Feed\Writer\Entry;
use Zend\Feed\Writer\Feed;
use Zend\Mvc\Controller\Plugin\Url;
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
     * @var Url
     */
    protected $url;

    /**
     * @param Feed $feed
     */
    public function __construct(Feed $feed, Url $url)
    {
        $this->feed = $feed;
        $this->url = $url;
    }

    /**
     * @param array $modules
     */
    public function addModules($modules)
    {
        foreach ($modules as $module) {
            $this->addModule($module);
        }
    }

    /**
     * @param ModuleEntity $module
     * @return Entry
     */
    public function addModule(ModuleEntity $module)
    {
        if (empty($module->getDescription())) {
            $moduleDescription = 'No description available';
        } else {
            $moduleDescription = $module->getDescription();
        }
        $moduleName = $module->getName();
        $urlParams = ['vendor' => $module->getOwner(), 'module' => $moduleName];

        $entry = $this->feed->createEntry();
        $entry->setTitle($moduleName);
        $entry->setDescription($moduleDescription);
        $entry->setLink($this->url->fromRoute('view-module', $urlParams, ['force_canonical' => true]));
        $entry->addAuthor(['name' => $module->getOwner()]);
        $entry->setDateCreated($module->getCreatedAtDateTime());
        $entry->setId(implode('/', $urlParams));

        $this->feed->addEntry($entry);

        return $entry;
    }
}
