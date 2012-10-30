<?php

namespace User\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;

class UserRepositories extends AbstractHelper implements ServiceManagerAwareInterface, EventManagerAwareInterface
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate;

        /**
     * @var ServiceManager
     */
    protected $serviceManager;

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
        $sm = $this->getServiceManager();

        $sm = $sm->getServiceLocator();
        $client = $sm->get('EdpGithub\Client');

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

        $mapper = $sm->get('application_module_mapper');
        foreach($repositories as $key => $repo) {
            if($repo->fork) {
                unset($repositories[$key]);
            } else {
                $module = $mapper->findByName($repo->name);
                if($module) {
                    unset($repositories[$key]);
                } else {
                    $em = $client->getHttpClient()->getEventManager();
                    $errorListener = $sm->get('EdpGithub\Listener\Error');
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
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
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
