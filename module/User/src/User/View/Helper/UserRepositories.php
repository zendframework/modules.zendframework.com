<?php

namespace User\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class UserRepositories extends AbstractHelper implements ServiceManagerAwareInterface
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate;

        /**
     * @var ServiceManager
     */
    protected $serviceManager;

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

        //need to fetch top lvl ServiceManager
        $sm = $sm->getServiceLocator();
        $api = $sm->get('edpgithub_api_factory');

        $service = $api->getService('Repo');
        $mapper = $sm->get('application_module_mapper');

        $params['per_page'] = 100;
        $params['page'] = 1;
        $ownerRepos = $service->listRepositories(null, 'owner', $params);
        if($api->getNext() && $api->getNext() != $api->getCurrent()) {
            $params['page'] = $api->getNext();
            $this->getAllRepos($ownerRepos, $params);
        }

        $memberRepos = $service->listRepositories(null, 'member', $params);
        if($api->getNext() && $api->getNext() != $api->getCurrent()) {
            $params['page'] = $api->getNext();
            $this->getAllRepos($ownerRepos, $params);
        }

        $repositories = array_merge($ownerRepos, $memberRepos);

        foreach($repositories as $key => $repo) {
            if($repo->getFork()) {
                unset($repositories[$key]);
            } else {
                $module = $mapper->findByName($repo->getName());
                if($module) {
                    unset($repositories[$key]);
                } 
            }
        }

        return $repositories;
    }    

    public function getAllRepos(&$repos,  $params) {
        $sm = $this->getServiceManager();

        //need to fetch top lvl ServiceManager
        $sm = $sm->getServiceLocator();
        $api = $sm->get('edpgithub_api_factory');
        $service = $api->getService('Repo');

        $repos = array_merge($repos, $service->listRepositories(null, 'owner', $params));
        if($api->getNext() && $api->getNext() != $params['page']) {
            $params['page'] = $api->getNext();
            $this->getAllRepos($repos, $params);
        }
       
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
}
