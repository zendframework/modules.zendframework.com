<?php

namespace User\View\Helper;

use EdpGithub\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

class UserOrganizationsFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $helperPluginManager
     * @return UserOrganizations
     */
    public function createService(ServiceLocatorInterface $helperPluginManager)
    {
        /* @var HelperPluginManager $helperPluginManager */
        $serviceManager = $helperPluginManager->getServiceLocator();

        /* @var Client $githubClient */
        $githubClient = $serviceManager->get('EdpGithub\Client');

        return new UserOrganizations($githubClient);
    }
}
