<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ModuleDescription extends AbstractHelper
{
    /**
     * @param array $module
     * @return string
     */
    public function __invoke(array $module)
    {
        return $this->getView()->render('zf-module/helper/module-description.phtml', [
            'module' => $module,
        ]);
    }
}
