<?php

namespace ZfModule\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TotalModulesFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $moduleMapper = $sm->get('zfmodule_mapper_module');

        return new TotalModules($moduleMapper);
    }
}
