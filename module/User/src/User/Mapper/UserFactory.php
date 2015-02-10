<?php

namespace User\Mapper;

use Zend\Db;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Options;

class UserFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceManager
     * @return User
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        /* @var Options\ModuleOptions $options */
        $options = $serviceManager->get('zfcuser_module_options');

        $entityClass = $options->getUserEntityClass();

        /* @var Db\Adapter\Adapter $dbAdapter */
        $dbAdapter = $serviceManager->get('zfcuser_zend_db_adapter');

        $mapper = new User();

        $mapper->setDbAdapter($dbAdapter);
        $mapper->setEntityPrototype(new $entityClass());
        $mapper->setHydrator(new UserHydrator());

        return $mapper;
    }
}
