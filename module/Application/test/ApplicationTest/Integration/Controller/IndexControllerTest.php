<?php

namespace ApplicationTest\Integration\Controller;

use Application\Controller;
use ApplicationTest\Integration\Util\Bootstrap;
use Zend\Http;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use PHPUnit_Framework_TestCase;

class IndexControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Controller\IndexController;
     */
    protected $controller;
    protected $request;
    protected $response;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    protected $event;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();

        /* @var ControllerManager $controllerManager */
        $controllerManager = $serviceManager->get('ControllerManager');
        $this->controller = $controllerManager->get('Application\Controller\Index');

        $this->request = new Http\Request();
        $this->routeMatch = new RouteMatch(['controller' => 'index']);
        $this->event = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : [];
        $router = HttpRouter::factory($routerConfig);
        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->routeMatch->setParam('action', 'index');

        $this->controller->dispatch($this->request);

        /* @var Response $response */
        $response = $this->controller->getResponse();

        $this->assertEquals(Http\Response::STATUS_CODE_200, $response->getStatusCode());
    }
}
