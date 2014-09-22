<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Feed\Writer\Feed;
use Zend\View\Model\FeedModel;

class IndexController extends AbstractActionController
{
    const MODULES_PER_PAGE = 15;

    public function indexAction()
    {
        $query =  $this->params()->fromQuery('query', null);
        $page = (int) $this->params('page', 1);

        $mapper = $this->getServiceLocator()->get('zfmodule_mapper_module');

        $repositories = $mapper->pagination($page, self::MODULES_PER_PAGE, $query, 'created_at', 'DESC');

        return array(
            'repositories' => $repositories,
            'query' => $query,
        );
    }

    /**
     * RSS feed for recently added modules
     * @return FeedModel
     */
    public function feedAction()
    {
        // Prepare the feed
        $feed = new Feed();
        $feed->setTitle('ZF2 Modules');
        $feed->setDescription('Recently added modules.');
        $feed->setFeedLink('http://modules.zendframework.com/feed', 'atom');
        $feed->setLink('http://modules.zendframework.com');

        // Get the recent modules
        $page = 1;
        $mapper = $this->getServiceLocator()->get('zfmodule_mapper_module');
        $repositories = $mapper->pagination($page, self::MODULES_PER_PAGE, null, 'created_at', 'DESC');

        // Load them into the feed
        foreach ($repositories as $module) {
            $entry = $feed->createEntry();
            $entry->setTitle($module->getName());

            if($module->getDescription() == '') {
                $moduleDescription = "No Description available";
            } else {
                $moduleDescription = $module->getDescription();
            }

            $entry->setDescription($moduleDescription);
            $entry->setLink($module->getUrl());
            $entry->setDateCreated(strtotime($module->getCreatedAt()));

            $feed->addEntry($entry);
        }

        // Render the feed
        $feedmodel = new FeedModel();
        $feedmodel->setFeed($feed);

        return $feedmodel;
    }

}
