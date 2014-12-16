<?php

namespace Application\Service;

use EdpGithub\Client;

class GithubService
{
    /**
     * @var Client
     */
    private $githubClient;

    /**
     * @param Client $githubClient
     */
    public function __construct(Client $githubClient) {
        $this->githubClient = $githubClient;
    }

    /**
     * Return the Repository Cache Key
     *
     * @param $user
     * @param $module
     * @return string
     */
    public function getRepositoryCacheKey($user, $module)
    {
        return 'module-view-' . $user . '-' . $module;
    }

    /**
     * Return MetaData from User Repository
     *
     * @param $user
     * @param $module
     * @return bool|mixed
     */
    public function getRepositoryMetadata($user, $module)
    {
        Try {
            $apiResponse = $this->githubClient->api('repos')->show($user, $module);
            return json_decode($apiResponse);
        } Catch(\Exception $e)
        {
            return false;
        }
    }

    /**
     * Get all User Repositories
     *
     * @param $user
     * @param array $params
     * @return mixed
     */
    public function getUserRepositories($user, $params = array())
    {
        return $this->githubClient->api('user')->repos($user, $params);
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

        if( !isset($contentResponse->content) ){
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
        Try {
            $apiResponse = $this->githubClient->api('repos')->content($user, $module, $filePath);
            $apiResponse = json_decode($apiResponse);
            return $apiResponse;

        } Catch(\Exception $e) {
            return false;
        }
    }

    /**
     * Return all Repositories from Auth User
     *
     * @param array $params
     * @return mixed
     */
    public function getAuthUserRepositories($params = array())
    {
        return $this->githubClient->api('current_user')->repos($params);
    }
}