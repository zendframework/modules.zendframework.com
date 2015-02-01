<?php

namespace Application\View\Helper;

use Zend\Mvc\Controller\Plugin\AbstractPlugin as Plugin;
use Zend\View\Helper\AbstractHelper;

class FlashMessenger extends AbstractHelper
{
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function __invoke()
    {
        return $this->plugin;
    }
}
