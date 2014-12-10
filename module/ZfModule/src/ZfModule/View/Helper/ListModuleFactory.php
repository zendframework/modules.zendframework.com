<?php

namespace ZfModule\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ListModuleFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $moduleMapper = $sm->get('zfmodule_mapper_module');
        $githubClient = $sm->get('EdpGithub\Client');

        return new ListModule($moduleMapper, $githubClient);
    }
}
