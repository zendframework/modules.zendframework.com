<?php

namespace ZfModule\Service;

use EdpGithub\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        /** @var \ZfModule\Mapper\Module $moduleMapper */
        $moduleMapper = $serviceLocator->get('zfmodule_mapper_module');

        /** @var Client $githubClient */
        $githubClient = $serviceLocator->get('EdpGithub\Client');

        $service = new Module(
            $moduleMapper,
            $githubClient
        );
        return $service;
    }
}