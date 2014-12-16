<?php

namespace Application\Service;

use EdpGithub\Client;
use EdpGithub\Collection\RepositoryCollection;
use EdpGithub\Listener\Exception\RuntimeException;

class RepositoryRetriever
{
    /**
     * @var Client
     */
    private $githubClient;

    /**
     * @param Client $githubClient
     */
    public function __construct(Client $githubClient)
    {
        $this->githubClient = $githubClient;
    }

    /**
     * Return MetaData from User Repository
     *
     * @param $user
     * @param $module
     * @return bool|mixed
     */
    public function getUserRepositoryMetadata($user, $module)
    {
        try {
            $apiResponse = $this->githubClient->api('repos')->show($user, $module);
            return json_decode($apiResponse);
        } catch (RuntimeException $e) {
            return false;
        }
    }

    /**
     * Get all Repositories from GitHub User
     *
     * @param $user
     * @param array $params
     */
    public function getUserRepositories($user, $params = array())
    {
        $repositoryCollection = $this->githubClient->api('user')->repos($user, $params);
        if( $repositoryCollection instanceof RepositoryCollection )
        {
            foreach($repositoryCollection as $repository)
            {
                yield $repository;
            }
        }
    }

    /**
     * Get File Content from User Repository
     *
     * @param $user
     * @param $module
     * @param $filePath
     * @return bool|string
     */
    public function getRepositoryFileContent($user, $module, $filePath)
    {
        $contentResponse = $this->getRepositoryFileMetadata($user, $module, $filePath);

        if (!isset($contentResponse->content)) {
            return false;
        }

        return base64_decode($contentResponse->content);
    }

    /**
     * Return File MetaData from User Repository
     *
     * @param $user
     * @param $module
     * @param $filePath
     * @return bool|mixed
     */
    public function getRepositoryFileMetadata($user, $module, $filePath)
    {
        try {
            $apiResponse = $this->githubClient->api('repos')->content($user, $module, $filePath);
            $apiResponse = json_decode($apiResponse);
            return $apiResponse;

        } catch (RuntimeException $e) {
            return false;
        }
    }

    /**
     * Return all Repositories from current authenticated GitHub User
     *
     * @param array $params
     */
    public function getAuthUserRepositories($params = array())
    {
        $repositoryCollection = $this->githubClient->api('current_user')->repos($params);
        if( $repositoryCollection instanceof RepositoryCollection )
        {
            foreach($repositoryCollection as $repository)
            {
                yield $repository;
            }
        }
    }
}
