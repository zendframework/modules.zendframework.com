<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ListModules extends AbstractHelper implements ServiceLocatorAwareInterface
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
     * __invoke
     *
     * @access public
     * @param array $options array of options
     * @return array Array of modules
     */
    public function __invoke($options = null)
    {
        $sl = $this->getServiceLocator();

        //need to fetch top lvl ServiceLocator
        $sl = $sl->getServiceLocator();
        $mapper = $sl->get('application_module_mapper');

        $user = isset($options['user'])? $options['user']:false;
        $modules = array();

        //limit modules to only user modules
        if($user) {
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

            foreach($repositories as $key => $repo) {
                $module = $mapper->findByName($repo->name);
                if($module) {
                    $modules[] = $module;
                }
            }
        } else {
            $limit = isset($options['limit'])?$options['limit']:null;

            $mapper = $sl->get('application_module_mapper');
            $modules = $mapper->findAll($limit, 'created_at', 'DESC');


        }
        return $modules;
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
}
