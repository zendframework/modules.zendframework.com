<?php

namespace ZfModule\Service;

use EdpGithub\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfModule\Mapper;

class ModuleFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Module
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var Mapper\Module $moduleMapper */
        $moduleMapper = $serviceLocator->get('zfmodule_mapper_module');

        /* @var Client $githubClient */
        $githubClient = $serviceLocator->get('EdpGithub\Client');

        return new Module($moduleMapper, $githubClient);
    }
}
