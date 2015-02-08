<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ModuleDescription extends AbstractHelper
{
    /**
     * __invoke
     *
     * @access public
     * @return string
     */
    public function __invoke($module)
    {
        return $this->getView()->render('zf-module/helper/module-description.phtml', [
            'module' => $module,
        ]);
    }
}
