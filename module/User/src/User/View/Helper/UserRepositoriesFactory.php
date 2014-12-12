<?php

namespace User\View\Helper;

use EdpGithub\Client;
use EdpGithub\Listener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;
use ZfModule\Mapper;

class UserRepositoriesFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return UserRepositories
     */
    public function createService(ServiceLocatorInterface $helperPluginManager)
    {
        /* @var HelperPluginManager $helperPluginManager */
        $serviceManager = $helperPluginManager->getServiceLocator();

        /* @var Mapper\Module $moduleMapper */
        $moduleMapper = $serviceManager->get('zfmodule_mapper_module');

        /* @var Client $githubClient */
        $githubClient = $serviceManager->get('EdpGithub\Client');

        /* @var Listener\Error $errorListener */
        $errorListener = $serviceManager->get('EdpGithub\Listener\Error');

        return new UserRepositories(
            $moduleMapper,
            $githubClient,
            $errorListener
        );
    }
}
