<?php

namespace ZfModule\Service;

use EdpGithub\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfModule\Mapper;

class ModuleFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return Module
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var Mapper\Module $moduleMapper */
        $moduleMapper = $serviceLocator->get(Mapper\Module::class);

        /* @var Client $githubClient */
        $githubClient = $serviceLocator->get('EdpGithub\Client');

        return new Module($moduleMapper, $githubClient);
    }
}
