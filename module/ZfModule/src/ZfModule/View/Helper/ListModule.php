<?php

namespace ZfModule\View\Helper;

use EdpGithub\Client;
use Zend\View\Helper\AbstractHelper;
use ZfModule\Mapper\Module;

class ListModule extends AbstractHelper
{
    /** @var Module */
    protected $moduleMapper;

    /** @var Client */
    protected $githubClient;

    /**
     * Constructor
     *
     * @param Module $moduleMapper
     * @param Client $githubClient
     */
    public function __construct(Module $moduleMapper, Client $githubClient)
    {
        $this->moduleMapper = $moduleMapper;
        $this->githubClient = $githubClient;
    }

    /**
     * Return Module Db Mapper
     *
     * @return Module
     */
    public function getModuleMapper()
    {
        return $this->moduleMapper;
    }

    /**
     * Return GithubClient
     *
     * @return Client
     */
    public function getGithubClient()
    {
        return $this->githubClient;
    }

    /**
     * __invoke
     *
     * @access public
     * @param array $options array of options
     * @return array Array of modules
     */
    public function __invoke($options = null)
    {
        //need to fetch top lvl ServiceLocator
        $user = isset($options['user'])? $options['user']:false;

        //limit modules to only user modules
        if ($user) {
            $repositories = $this->getGithubClient()->api('current_user')->repos(array('type' =>'all', 'per_page' => 100));

            $modules = array();
            foreach ($repositories as $repository) {
                if (!$repository->fork && $repository->permissions->push) {
                    $module = $this->getModuleMapper()->findByName($repository->name);
                    if ($module) {
                        $modules[] = $module;
                    }
                }
            }
        } else {
            $limit = isset($options['limit'])?$options['limit']:null;
            $modules = $this->getModuleMapper()->findAll($limit, 'created_at', 'DESC');
        }
        return $modules;
    }
}
