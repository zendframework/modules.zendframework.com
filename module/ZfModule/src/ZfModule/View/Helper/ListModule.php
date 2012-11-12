<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ListModule extends AbstractHelper implements ServiceLocatorAwareInterface
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
        $mapper = $sl->get('zfmodule_mapper_module');

        $user = isset($options['user'])? $options['user']:false;
        $modules = array();

        //limit modules to only user modules
        if($user) {
            $client = $sl->get('EdpGithub\Client');

            $repos = $client->api('current_user')->repos(array('type' =>'all', 'per_page' => 100));

            $modules = array();
            foreach($repos as $repo) {
                if(!$repo->fork && $repo->permissions->push) {
                    $module = $mapper->findByName($repo->name);
                    if($module) {
                       $modules[] = $module;
                    }
                }
            }
        } else {
            $limit = isset($options['limit'])?$options['limit']:null;

            $mapper = $sl->get('zfmodule_mapper_module');
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
