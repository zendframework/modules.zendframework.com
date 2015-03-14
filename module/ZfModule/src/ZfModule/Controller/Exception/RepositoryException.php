<?php

namespace ZfModule\Controller\Exception;

use Application\Exception;
use Zend\Http;

final class RepositoryException extends Exception\ViewableUserException
{
    /**
     * Generates an Exception when an invalid Repository is requested
     *
     * @param string $publicMessage
     * @param string $repositoryOwner
     * @param string $repositoryName
     * @return self
     */
    public static function fromNotFoundRepository($publicMessage, $repositoryOwner, $repositoryName)
    {
        return new self(
            sprintf('Invalid Repository requested [%s/%s]', $repositoryOwner, $repositoryName),
            (string) $publicMessage,
            Http\Response::STATUS_CODE_404
        );
    }

    /**
     * Generates an Exception when an invalid Repository by Url is requested
     *
     * @param string $publicMessage
     * @param string $repositoryUrl
     * @return self
     */
    public static function fromNotFoundRepositoryUrl($publicMessage, $repositoryUrl)
    {
        return new self(
            sprintf('Invalid Repository from URL requested [%s]', $repositoryUrl),
            (string) $publicMessage,
            Http\Response::STATUS_CODE_404
        );
    }

    /**
     * Generates an Exception when an Repository is requested with invalid permissions
     *
     * @param string $publicMessage
     * @param string $repositoryIdentifier
     * @param string[] $requiredPermissions
     * @return self
     */
    public static function fromInsufficientPermissions($publicMessage, $repositoryIdentifier, array $requiredPermissions)
    {
        return new self(
            sprintf('Invalid Repository permission [%s] required [%s]', $repositoryIdentifier, implode(',', $requiredPermissions)),
            (string) $publicMessage,
            Http\Response::STATUS_CODE_403
        );
    }

    /**
     * Generates an Exception when an Repository is not a ZF Module
     *
     * @param string $publicMessage
     * @param string $repositoryIdentifier
     * @return self
     */
    public static function fromNonModuleRepository($publicMessage, $repositoryIdentifier)
    {
        return new self(
            sprintf('Invalid Repository - No ZF Module [%s]', $repositoryIdentifier),
            (string) $publicMessage,
            Http\Response::STATUS_CODE_403
        );
    }
}
