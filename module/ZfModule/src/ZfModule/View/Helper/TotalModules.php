<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TotalModules extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocator
     */
    protected $serviceLocator;

    /**
     * @var int
     */
    protected $total;

    /**
     * __invoke
     *
     * @access public
     * @return string
     */
    public function __invoke()
    {
        if ($this->total === null) {
            $sl = $this->getServiceLocator();

            //need to fetch top lvl ServiceLocator: ServiceManager
            $sm = $sl->getServiceLocator();
            $mapper = $sm->get('zfmodule_mapper_module');
            $this->total = $mapper->getTotal();
        }
        return $this->total;
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
