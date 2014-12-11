<?php

namespace ZfModule\View\Helper;

use EdpGithub\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;
use ZfModule\Mapper;

class ListModuleFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $helperPluginManager
     * @return ListModule
     */
    public function createService(ServiceLocatorInterface $helperPluginManager)
    {
        /* @var HelperPluginManager $helperPluginManager */
        $serviceManager = $helperPluginManager->getServiceLocator();

        /* @var Mapper\Module $moduleMapper */
        $moduleMapper = $serviceManager->get('zfmodule_mapper_module');

        /* @var Client $githubClient */
        $githubClient = $serviceManager->get('EdpGithub\Client');

        return new ListModule(
            $moduleMapper,
            $githubClient
        );
    }
}
