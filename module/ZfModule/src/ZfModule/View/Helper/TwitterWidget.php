<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;

class TwitterWidget extends AbstractHelper
{
    /**
     * @return string
     */
    public function __invoke()
    {
        return $this->getView()->render('zf-module/helper/twitter-widget-view.phtml');
    }
}
