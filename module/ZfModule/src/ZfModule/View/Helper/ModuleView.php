<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ModuleView extends AbstractHelper
{
    /**
     * __invoke
     *
     * @access public
     * @return string
     */
    public function __invoke($module, $button = 'submit')
    {
        return $this->getView()->render('zf-module/helper/module-view.phtml', [
            'module' => $module,
            'button' => $button,
        ]);
    }
}
