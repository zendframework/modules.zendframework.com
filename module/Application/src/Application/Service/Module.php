<?php

namespace Application\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Hydrator\ClassMethods;
use ZfcBase\EventManager\EventProvider;
use Application\Mapper\ModuleInterface as ModuleMapperInterface;

class Module extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var ModuleMapperInterface
     */
    protected $moduleMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * createFromForm
     *
     * @param array $data
     * @return \ZfcUser\Entity\UserInterface
     * @throws Exception\InvalidArgumentException
     */
    public function register($data)
    {
        $url = $data->html_url;
        $module = $this->getModuleMapper()->findByUrl($url);
        $update = true;
        if(!$module) {
            $module  = new \Application\Entity\Module;
            $update = false;
        }

        $module->setName($data->name);
        $module->setDescription($data->description);
        $module->setUrl($data->html_url);
        $owner = $data->owner;
        $module->setOwner($owner->login);
        $module->setPhotoUrl($owner->avatar_url);

        if($update) {
            $this->getModuleMapper()->update($module);
        } else {
            $this->getModuleMapper()->insert($module);
        }

        return $module;
    }

    /**
     * getModuleMapper
     *
     * @return ModuleMapperInterface
     */
    public function getModuleMapper()
    {
        if (null === $this->moduleMapper) {
            $this->moduleMapper = $this->getServiceManager()->get('application_module_mapper');
        }
        return $this->moduleMapper;
    }

    /**
     * setModuleMapper
     *
     * @param ModuleMapperInterface $moduleMapper
     * @return Module
     */
    public function setModuleMapper(ModuleMapperInterface $moduleMapper)
    {
        $this->moduleMapper = $moduleMapper;
        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
