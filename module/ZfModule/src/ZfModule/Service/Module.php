<?php

namespace ZfModule\Service;

use EdpGithub\Client;
use stdClass;
use ZfcBase\EventManager\EventProvider;
use ZfModule\Mapper\Module as ModuleMapper;

class Module extends EventProvider
{
    /** @var Module */
    private $moduleMapper;

    /** @var Client */
    private $githubClient;

    /**
     * Constructor
     *
     * @param ModuleMapper $moduleMapper
     * @param Client $githubClient
     */
    public function __construct(ModuleMapper $moduleMapper, Client $githubClient)
    {
        $this->moduleMapper = $moduleMapper;
        $this->githubClient = $githubClient;
    }

    /**
     * Return Module Db Mapper
     *
     * @return Module
     */
    protected function getModuleMapper()
    {
        return $this->moduleMapper;
    }

    /**
     * Return GithubClient
     *
     * @return Client
     */
    protected function getGithubClient()
    {
        return $this->githubClient;
    }

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
     * @param stdClass $repository
     * @return bool
     */
    public function isModule(stdClass $repository)
    {
        $query = 'repo:' . $repository->owner->login . '/' . $repository->name . ' filename:Module.php "class Module"';
        $response = $this->getGithubClient()->getHttpClient()->request('search/code?q=' . $query);
        $result = json_decode($response->getbody(), true);

        if (isset($result['total_count']) && $result['total_count'] > 0) {
            return true;
        }

        return false;
    }
}
