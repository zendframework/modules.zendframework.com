<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Feed\Writer\Feed;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\FeedModel;
use Zend\View\Model\ViewModel;
use ZfModule\Mapper;

class IndexController extends AbstractActionController
{
    const MODULES_PER_PAGE = 15;

    /**
     * @var Mapper\Module
     */
    private $moduleMapper;

    /**
     * @param Mapper\Module $moduleMapper
     */
    public function __construct(Mapper\Module $moduleMapper)
    {
        $this->moduleMapper = $moduleMapper;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $query =  $this->params()->fromQuery('query', null);
        $page = (int) $this->params()->fromQuery('page', 1);

        $repositories = $this->moduleMapper->pagination($page, self::MODULES_PER_PAGE, $query, 'created_at', 'DESC');

        return new ViewModel([
            'repositories' => $repositories,
            'query' => $query,
        ]);
    }

    /**
     * RSS feed for recently added modules
     * @return FeedModel
     */
    public function feedAction()
    {
        $url = $this->plugin('url');
        // Prepare the feed
        $feed = new Feed();
        $feed->setTitle('ZF2 Modules');
        $feed->setDescription('Recently added ZF2 modules');
        $feed->setFeedLink($url->fromRoute('feed', [], ['force_canonical' => true]), 'atom');
        $feed->setLink($url->fromRoute('home', [], ['force_canonical' => true]));

        // Get the recent modules
        $page = 1;
        $modules = $this->moduleMapper->pagination($page, self::MODULES_PER_PAGE, null, 'created_at', 'DESC');

        // Load them into the feed
        $mapper = new Mapper\ModuleToFeed($feed, $url);
        $mapper->addModules($modules);

        // Render the feed
        $feedmodel = new FeedModel();
        $feedmodel->setFeed($feed);

        return $feedmodel;
    }
}
