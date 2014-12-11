<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OwnerModuleList extends AbstractHelper implements ServiceLocatorAwareInterface
{
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

        //need to fetch top lvl serviceLocator
        $sl = $sl->getServiceLocator();

        $mapper = $sl->get('zfmodule_mapper_module');
        $modules = $mapper->findByOwner($options["owner"], 10, 'created_at', 'DESC');

        //return $modules;
        $vm = new ViewModel(array(
            'modules' => $modules,
        ));
        $vm->setTemplate('zf-module/helper/new-module.phtml');

        return $this->getView()->render($vm);
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
