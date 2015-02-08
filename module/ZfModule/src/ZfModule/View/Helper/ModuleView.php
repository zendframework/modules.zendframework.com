<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class ModuleView extends AbstractHelper
{
    /**
     * @param array $module
     * @param string $button
     * @return string
     */
    public function __invoke($module, $button = 'submit')
    {
        $vm = new ViewModel([
            'module' => $module,
            'button' => $button,
        ]);
        $vm->setTemplate('zf-module/helper/module-view.phtml');

        return $this->getView()->render($vm);
    }
}
