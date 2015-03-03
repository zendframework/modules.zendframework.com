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
        $this->owner = $owner;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        if ($this->url === null) {
            $this->url = sprintf('https://github.com/%s/%s', $this->owner, $this->name);
        }

        return $this->url;
    }
}
