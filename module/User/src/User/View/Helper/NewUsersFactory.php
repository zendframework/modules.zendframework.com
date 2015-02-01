<?php

namespace User\View\Helper;

use User\Mapper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

class NewUsersFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return NewUsers
     */
    public function createService(ServiceLocatorInterface $helperPluginManager)
    {
        /* @var HelperPluginManager $helperPluginManager */
        $serviceLocator = $helperPluginManager->getServiceLocator();

        /* @var Mapper\User $userMapper */
        $userMapper = $serviceLocator->get('zfcuser_user_mapper');

        return new NewUsers($userMapper);
    }
}
