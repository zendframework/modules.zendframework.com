<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

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
        $vm = new ViewModel(array(
            'module' => $module,
        ));
        $vm->setTemplate('zf-module/helper/module-description.phtml');


        return $this->getView()->render($vm);
    }
}
