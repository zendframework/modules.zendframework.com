<?php

namespace User\Controller;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfModule\Service;

class UserControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $controllerManager
     * @return UserController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /* @var ControllerManager $controllerManager */
        $serviceManager = $controllerManager->getServiceLocator();

        /* @var Service\Module $moduleService */
        $moduleService = $serviceManager->get(Service\Module::class);

        $redirectCallback = $controllerManager->getServiceLocator()->get('zfcuser_redirect_callback');

        return new UserController($redirectCallback, $moduleService);
    }
}
