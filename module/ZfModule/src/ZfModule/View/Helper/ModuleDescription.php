<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class ModuleDescription extends AbstractHelper
{
    /**
     * @param array $module
     * @return string
     */
    public function __invoke(array $module)
    {
        $vm = new ViewModel([
            'module' => $module,
        ]);
        $vm->setTemplate('zf-module/helper/module-description.phtml');

        return $this->getView()->render($vm);
    }
}
