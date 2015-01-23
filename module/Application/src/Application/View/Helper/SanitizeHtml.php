<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class SanitizeHtml extends AbstractHelper
{
    private $htmlPurifier;

    public function __construct(\HTMLPurifier $htmlPurifier)
    {
        $this->htmlPurifier = $htmlPurifier;
    }

    public function __invoke($dirtyHtml)
    {
        return $this->htmlPurifier->purify($dirtyHtml);
    }
}
