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

        //limit modules to only user modules
        if($user) {
            $api = $sm->get('edpgithub_api_factory');
            $service = $api->getService('Repo');
            $modules = array();
            $memberRepos = $service->listRepositories(null, 'member');
            $ownerRepos = $service->listRepositories(null, 'owner');

            foreach($ownerRepos as $key => $repo) {
                if($repo->getFork()) {
                    unset($ownerRepos[$key]);
                }
            }

            $repositories = array_merge($ownerRepos, $memberRepos);

            foreach($repositories as $key => $repo) {
                $module = $mapper->findByName($repo->getName());
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
