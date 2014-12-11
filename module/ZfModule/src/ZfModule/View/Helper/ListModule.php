<?php

namespace ZfModule\View\Helper;

use EdpGithub\Client;
use Zend\View\Helper\AbstractHelper;
use ZfModule\Mapper;

class ListModule extends AbstractHelper
{
    /**
     * @var Mapper\Module
     */
    private $moduleMapper;

    /**
     * @var Client
     */
    private $githubClient;

    /**
     * @param Mapper\Module $moduleMapper
     * @param Client $githubClient
     */
    public function __construct(Mapper\Module $moduleMapper, Client $githubClient)
    {
        $this->moduleMapper = $moduleMapper;
        $this->githubClient = $githubClient;
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
            $repositories = $this->githubClient->api('current_user')->repos([
                'type' =>'all',
                'per_page' => 100
            ]);

            $modules = array();
            foreach ($repositories as $repository) {
                if (!$repository->fork && $repository->permissions->push) {
                    $module = $this->moduleMapper->findByName($repository->name);
                    if ($module) {
                        $modules[] = $module;
                    }
                }
            }
        } else {
            $limit = isset($options['limit'])?$options['limit']:null;
            $modules = $this->moduleMapper->findAll($limit, 'created_at', 'DESC');
        }
        return $modules;
    }
}
