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
     * @param stdClass $repository
     * @return Entity\Module
     */
    public function register($repository)
    {
        $isUpdate = false;

        $module = $this->moduleMapper->findByUrl($repository->html_url);

        if ($module) {
            $isUpdate = true;
        } else {
            $module  = new Entity\Module();
        }

        $module->setName($repository->name);
        $module->setDescription($repository->description);
        $module->setUrl($repository->html_url);
        $module->setOwner($repository->owner->login);
        $module->setPhotoUrl($repository->owner->avatar_url);

        if ($isUpdate) {
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
     * @return stdClass[]
     */
    public function currentUserModules()
    {
        /* @var RepositoryCollection $repositoryCollection */
        $repositoryCollection = $this->githubClient->api('current_user')->repos([
            'type' => 'all',
            'per_page' => 100,
        ]);

        return array_filter(iterator_to_array($repositoryCollection), function ($repository) {
            if (true === $repository->fork) {
                return false;
            }

            if (false === $repository->permissions->push) {
                return false;
            }

            if (false === $this->moduleMapper->findByUrl($repository->html_url)) {
                return false;
            }

            return true;
        });
    }
}
