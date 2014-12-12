<?php

namespace User\View\Helper;

use EdpGithub\Client;
use EdpGithub\Listener;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\View\Helper\AbstractHelper;
use ZfModule\Mapper;

class UserRepositories extends AbstractHelper implements EventManagerAwareInterface
{
    /**
     * @var Mapper\Module
     */
    private $moduleMapper;

    /**
     * @var Client
     */
    private $githubClient;

    /**
     * @var Listener\Error
     */
    private $errorListener;

    /**
     * $var string template used for view
     */
    protected $viewTemplate;

    /**
     * @var EventManager
     */
    protected $events;

    /**
     * @param Mapper\Module $moduleMapper
     * @param Client $githubClient
     * @param Listener\Error $errorListener
     */
    public function __construct(Mapper\Module $moduleMapper, Client $githubClient, Listener\Error $errorListener)
    {
        $this->moduleMapper = $moduleMapper;
        $this->githubClient = $githubClient;
        $this->errorListener = $errorListener;
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        $repositories = array();

        $ownerRepos = $this->githubClient->api('current_user')->repos(array('type' =>'owner'));
        foreach ($ownerRepos as $repo) {
            if (!$repo->fork) {
                $repositories[] = $repo;
            }
        }

        $memberRepos = $this->githubClient->api('current_user')->repos(array('type' =>'member'));
        foreach ($memberRepos as $repo) {
            $repositories[] = $repo;
        }

        foreach ($repositories as $key => $repo) {
            if ($repo->fork) {
                unset($repositories[$key]);
            } else {
                $module = $this->moduleMapper->findByName($repo->name);
                if ($module) {
                    unset($repositories[$key]);
                } else {
                    $em = $this->githubClient->getHttpClient()->getEventManager();
                    $em->detachAggregate($this->errorListener);
                    $module = $this->githubClient->api('repos')->content($repo->full_name, 'Module.php');
                    $response = $this->githubClient->getHttpClient()->getResponse();
                    if (!$response->isSuccess()) {
                        unset($repositories[$key]);
                    }
                    $em->attachAggregate($this->errorListener);
                }
            }
        }

        return $repositories;
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
