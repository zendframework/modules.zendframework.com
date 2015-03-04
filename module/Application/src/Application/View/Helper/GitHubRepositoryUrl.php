<?php

namespace Application\View\Helper;

use Application\Entity;
use Zend\View\Helper\AbstractHelper;

class GitHubRepositoryUrl extends AbstractHelper
{
    /**
     * @var Entity\Repository
     */
    private $repository;

    /**
     * @var string
     */
    private $url;

    /**
     * @param Entity\Repository $repository
     */
    public function __construct(Entity\Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        if (null === $this->url) {
            $this->url = sprintf(
                'https://github.com/%s/%s',
                $this->repository->owner(),
                $this->repository->name()
            );
        }

        return $this->url;
    }
}
