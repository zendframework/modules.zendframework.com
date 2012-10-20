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

        $repoList = array();
        $service = $api->getService('Repo');
        $memberRepositories = $service->listRepositories(null, 'member');
       
        foreach($memberRepositories as $repo) {
            $repoList[$repo->getName()] = $repo;
        }

        $allRepositories = $service->listRepositories(null, 'all');
       
        foreach($allRepositories as $repo) {
            if(!$repo->getFork()) {
                $repoList[$repo->getName()] = $repo;
            }
        }
        
        return array('repositories' => $repoList, 'api' => $api);

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
