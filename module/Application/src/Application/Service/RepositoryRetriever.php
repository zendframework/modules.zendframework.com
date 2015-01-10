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
     * @param string $user
     * @param string $module
     * @return mixed
     */
    public function getUserRepositoryMetadata($user, $module)
    {
        return json_decode($this->githubClient->api('repos')->show($user, $module));
    }

    /**
     * Get all Repositories from GitHub User
     * @param string $user
     * @param array $params
     * @return RepositoryCollection
     */
    public function getUserRepositories($user, array $params = array())
    {
        return $this->githubClient->api('user')->repos($user, $params);
    }

    /**
     * Get File Content from User Repository
     * @param $user
     * @param $module
     * @param $filePath
     * @return string|null
     */
    public function getRepositoryFileContent($user, $module, $filePath)
    {
        $contentResponse = $this->getRepositoryFileMetadata($user, $module, $filePath);

        if (!isset($contentResponse->content)) {
            return null;
        }

        return base64_decode($contentResponse->content);
    }

    /**
     * Return File MetaData from User Repository
     * @param string $user
     * @param string $module
     * @param string $filePath
     * @return mixed
     */
    public function getRepositoryFileMetadata($user, $module, $filePath)
    {
        return json_decode($this->githubClient->api('repos')->content($user, $module, $filePath));
    }

    /**
     * Return all Repositories from current authenticated GitHub User
     * @param array $params
     * @return RepositoryCollection
     */
    public function getAuthenticatedUserRepositories(array $params = array())
    {
        return $this->githubClient->api('current_user')->repos($params);
    }
}
