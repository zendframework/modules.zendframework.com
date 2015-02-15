<?php

namespace ZfModule\Service;

use EdpGithub\Client;
use EdpGithub\Collection\RepositoryCollection;
use EdpGithub\Http\Client as HttpClient;
use stdClass;
use Zend\Http;
use ZfcBase\EventManager\EventProvider;
use ZfModule\Entity;
use ZfModule\Mapper;

class Module extends EventProvider
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
     * @param stdClass $data
     * @return object|Entity\Module
     */
    public function register($data)
    {
        $url = $data->html_url;
        $module = $this->moduleMapper->findByUrl($url);
        $update = true;
        if (!$module) {
            $module  = new Entity\Module();
            $update = false;
        }

        $module->setName($data->name);
        $module->setDescription($data->description);
        $module->setUrl($data->html_url);
        $owner = $data->owner;
        $module->setOwner($owner->login);
        $module->setPhotoUrl($owner->avatar_url);

        if ($update) {
            $this->moduleMapper->update($module);
        } else {
            $this->moduleMapper->insert($module);
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
        $query = sprintf(
            'repo:%s/%s filename:Module.php "class Module"',
            $repository->owner->login,
            $repository->name
        );

        $path = sprintf(
            'search/code?q=%s',
            $query
        );

        /* @var HttpClient $httpClient */
        $httpClient = $this->githubClient->getHttpClient();

        /* @var Http\Response $response */
        $response = $httpClient->request($path);

        $result = json_decode($response->getBody(), true);

        if (isset($result['total_count']) && $result['total_count'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $limit
     * @return Entity\Module[]
     */
    public function allModules($limit = null)
    {
        return $this->moduleMapper->findAll(
            $limit,
            'created_at',
            'DESC'
        );
    }

    /**
     * @return Entity\Module[]
     */
    public function currentUserModules()
    {
        /* @var RepositoryCollection $repositoryCollection */
        $repositoryCollection = $this->githubClient->api('current_user')->repos([
            'type' => 'all',
            'per_page' => 100,
        ]);

        $modules = [];

        foreach ($repositoryCollection as $repository) {
            if (true === $repository->fork) {
                continue;
            }

            if (false === $repository->permissions->push) {
                continue;
            }

            $module = $this->moduleMapper->findByName($repository->name);
            if (!($module instanceof Entity\Module)) {
                continue;
            }

            array_push($modules, $module);
        }

        return $modules;
    }
}
