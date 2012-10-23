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

        $sm = $sm->getServiceLocator();
        $service = $sm->get('application_service_repository');

        $ownerRepos = $service->getAllRepository('owner');
        $memberRepos = $service->getAllRepository('member');


        $repositories = array_merge($ownerRepos, $memberRepos);

        $mapper = $sm->get('application_module_mapper');

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
