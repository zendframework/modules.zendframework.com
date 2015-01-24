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
     * @param string $user
     * @param string $module
     *
     * @return mixed
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
     * @param string $user
     * @param array $params
     *
     * @return RepositoryCollection
     */
    public function getUserRepositories($user, $params = [])
    {
        return $this->githubClient->api('user')->repos($user, $params);
    }

    /**
     * Get File Content from User Repository
     *
     * @param string $user
     * @param string $module
     * @param string $filePath
     * @param bool $parseMarkdown
     *
     * @return bool|string
     */
    public function getRepositoryFileContent($user, $module, $filePath, $parseMarkdown = false)
    {
        $contentResponse = $this->getRepositoryFileMetadata($user, $module, $filePath);

        if (!isset($contentResponse->content)) {
            return false;
        }

        $content = base64_decode($contentResponse->content);
        if ($content && $parseMarkdown) {
            return $this->requestContentMarkdown($content);
        }

        return $content;
    }

    /**
     * Request content as parsed markdown
     *
     * @param string $content
     *
     * @return string|null
     */
    private function requestContentMarkdown($content)
    {
        try {
            return $this->githubClient->api('markdown')->render($content);
        } catch (RuntimeException $e) {
            return null;
        }
    }

    /**
     * Return File MetaData from User Repository
     *
     * @param string $user
     * @param string $module
     * @param string $filePath
     *
     * @return bool|string
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
     *
     * @return RepositoryCollection
     */
    public function getAuthenticatedUserRepositories($params = [])
    {
        return $this->githubClient->api('current_user')->repos($params);
    }
}
