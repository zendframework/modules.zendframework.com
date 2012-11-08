<?php

namespace User\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;

class UserRepositories extends AbstractHelper implements ServiceLocatorAwareInterface, EventManagerAwareInterface
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate;

    /**
     * @var ServiceLocator
     */
    protected $serviceLocator;

    /**
     * @var EventManager
     */
    protected $events;

    /**
     * __invoke
     *
     * @access public
     * @param array $options array of options
     * @return string
     */
    public function __invoke($options = array())
    {
        $sl = $this->getServiceLocator();

        //fetch top lvl ServiceLocator
        $sl = $sl->getServiceLocator();
        $client = $sl->get('EdpGithub\Client');

        $repositories = array();

        $ownerRepos = $client->api('current_user')->repos(array('type' =>'owner'));
        foreach($ownerRepos as $repo) {
            if(!$repo->fork) {
                $repositories[] = $repo;
            }
        }

        $memberRepos = $client->api('current_user')->repos(array('type' =>'member'));
        foreach($memberRepos as $repo) {
            $repositories[] = $repo;
        }

        $mapper = $sl->get('application_module_mapper');
        foreach($repositories as $key => $repo) {
            if($repo->fork) {
                unset($repositories[$key]);
            } else {
                $module = $mapper->findByName($repo->name);
                if($module) {
                    unset($repositories[$key]);
                } else {
                    $em = $client->getHttpClient()->getEventManager();
                    $errorListener = $sl->get('EdpGithub\Listener\Error');
                    $em->detachAggregate($errorListener);
                    $module = $client->api('repos')->content($repo->full_name, 'Module.php');
                    $response = $client->getHttpClient()->getResponse();
                    if(!$response->isSuccess()){
                        unset($repositories[$key]);
                    }
                    $em->attachAggregate($errorListener);
                }
            }
        }

        return $repositories;
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Set Event Manager
     *
     * @param EventManagerInterface $events
     * @return Client
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->events = $events;
        return $this;
    }

    /**
     * Get Event Manager
     * @return EventManager
     */
    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}
