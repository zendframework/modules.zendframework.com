<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GitHubRepositoryUrl extends AbstractHelper
{
    /**
     * @var string
     */
    private $owner;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url;

    /**
     * @param string $owner
     * @param string $name
     */
    public function __construct($owner, $name)
    {
        $this->owner = (string) $owner;
        $this->name = (string) $name;
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        if (null === $this->url) {
            $this->url = sprintf(
                'https://github.com/%s/%s',
                $this->owner,
                $this->name
            );
        }

        return $this->url;
    }
}
