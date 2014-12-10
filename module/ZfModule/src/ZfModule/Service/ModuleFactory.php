<?php

namespace ZfModule\Service;

use EdpGithub\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfModule\Mapper\Module;

class ModuleFactory implements FactoryInterface
{
    /**
     * Create Service Instance
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed|Module
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ModuleMapper $moduleMapper */
        $moduleMapper = $serviceLocator->get('zfmodule_mapper_module');

        /** @var Client $githubClient */
        $githubClient = $serviceLocator->get('EdpGithub\Client');

        return new Module($moduleMapper, $githubClient);
    }
}
