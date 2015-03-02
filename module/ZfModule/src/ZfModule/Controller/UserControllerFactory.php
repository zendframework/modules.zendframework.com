<?php

namespace ZfModule\Controller;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfModule\Mapper;
use ZfModule\Service;

class UserControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $controllerManager
     * @return IndexController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /* @var ControllerManager $controllerManager */
        $serviceManager = $controllerManager->getServiceLocator();

        /* @var Mapper\Module $moduleMapper */
        $moduleMapper = $serviceManager->get(Mapper\Module::class);

        /* @var Service\Module $moduleService */
        $moduleService = $serviceManager->get(Service\Module::class);

        return new UserController(
            $moduleMapper,
            $moduleService
        );
    }
}
