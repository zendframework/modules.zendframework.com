<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class ListModules extends AbstractHelper implements ServiceManagerAwareInterface
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
     * @return array Array of modules
     */
    public function __invoke($options = null)
    {
        $sm = $this->getServiceManager();

        //need to fetch top lvl ServiceManager
        $sm = $sm->getServiceLocator();
        $mapper = $sm->get('application_module_mapper');

        $user = isset($options['user'])? $options['user']:false;
        $modules = array();

        //limit modules to only user modules
        if($user) {
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

            foreach($repositories as $key => $repo) {
                $module = $mapper->findByName($repo->name);
                if($module) {
                    $modules[] = $module;
                }
            }
        } else {
            $limit = isset($options['limit'])?$options['limit']:null;

            $mapper = $sm->get('application_module_mapper');
            $modules = $mapper->findAll($limit, 'created_at');


        }
        return $modules;
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
