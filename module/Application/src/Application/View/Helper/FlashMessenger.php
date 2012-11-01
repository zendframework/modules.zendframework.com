<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\Plugin\AbstractPlugin as Plugin;

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
