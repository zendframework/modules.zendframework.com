<?php

namespace ZfModule\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Module Mapper Factory
 */
class ModuleFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $hydrator = $serviceLocator->get(ModuleHydrator::class);
        $dbAdapter = $serviceLocator->get('zfcuser_zend_db_adapter');

        $mapper = new Module();
        $mapper->setDbAdapter($dbAdapter);
        $mapper->setEntityPrototype(new \ZfModule\Entity\Module());
        $mapper->setHydrator($hydrator);

        return $mapper;
    }

}
