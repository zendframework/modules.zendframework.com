<?php

namespace ZfModule\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
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
        if (!$module) {
            $module  = new \ZfModule\Entity\Module;
            $update = false;
        }

        $module->setName($data->name);
        $module->setDescription($data->description);
        $module->setUrl($data->html_url);
        $owner = $data->owner;
        $module->setOwner($owner->login);
        $module->setPhotoUrl($owner->avatar_url);

        if ($update) {
            $this->getModuleMapper()->update($module);
        } else {
            $this->getModuleMapper()->insert($module);
        }

        return $module;
    }

    /**
     * Check if Repo is a ZF Module
     *
     * @param  array  $repository
     * @return boolean
     */
    public function isModule($repository)
    {
        $sm = $this->getServiceLocator();
        $client = $sm->get('EdpGithub\Client');

        if (!json_decode($repository) instanceof \stdClass) {
            return false;
        }
        $query = 'repo:' . $repository->owner->login . '/' . $repository->name . ' filename:Module.php "class Module"';
        $response = $client->getHttpClient()->request('search/code?q=' . $query);
        $result = json_decode($response->getbody(), true);

        if (isset($result['total_count']) && $result['total_count'] > 0) {
            return true;
        }

        return false;
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
