<?php

namespace ZfModule\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;
use ZfModule\Mapper;

class NewModuleFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $helperPluginManager
     * @return NewModule
     */
    public function createService(ServiceLocatorInterface $helperPluginManager)
    {
        /* @var HelperPluginManager $helperPluginManager */
        $serviceManager = $helperPluginManager->getServiceLocator();

        /* @var Mapper\Module $moduleMapper */
        $moduleMapper = $serviceManager->get('zfmodule_mapper_module');

        return new NewModule($moduleMapper);
    }
}
