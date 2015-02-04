<?php

namespace ZfModule\Mvc\Controller\Plugin;

use EdpGithub\Client;
use Zend\Mvc\Controller\PluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfModule\Mapper;

class ListModuleFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return ListModule
     */
    public function createService(ServiceLocatorInterface $pluginManager)
    {
        /* @var PluginManager $pluginManager */
        $serviceManager = $pluginManager->getServiceLocator();

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
