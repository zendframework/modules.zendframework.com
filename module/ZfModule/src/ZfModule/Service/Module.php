<?php

namespace ZfModule\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;;
use Zend\Stdlib\Hydrator\ClassMethods;
use ZfcBase\EventManager\EventProvider;
use ZfModule\Mapper\ModuleInterface as ModuleMapperInterface;

class Module extends EventProvider implements ServiceLocatorAwareInterface
{

    /**
     * @var ModuleMapperInterface
     */
    protected $moduleMapper;

    /**
     * @var ServiceLocator
     */
    protected $serviceLocator;

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
            $module  = new \ZfModule\Entity\Module;
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
     * Check if Repo is a ZF Module
     *
     * @param  array  $repo
     * @return boolean
     */
    public function isModule($repo)
    {
        $sm = $this->getServiceLocator();
        $client = $sm->get('EdpGithub\Client');
        try{
            $module = $client->api('repos')->content($repo->owner->login, $repo->name, 'Module.php');
        } catch(\Exception $e) {
            return false;
        }

        if(!json_decode($module) instanceOf \stdClass) {
            return false;
        }

        return true;
    }

    /**
     * getModuleMapper
     *
     * @return ModuleMapperInterface
     */
    public function getModuleMapper()
    {
        if (null === $this->moduleMapper) {
            $this->moduleMapper = $this->getServiceLocator()->get('zfmodule_mapper_module');
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
     * Retrieve Service Locator instance
     *
     * @return ServiceLocator
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set Service Locator instance
     *
     * @param ServiceLocator $locator
     * @return User
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
