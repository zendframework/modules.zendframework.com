<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class ModuleDescription extends AbstractHelper
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate;

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

    /**
     * @param string $viewTemplate
     * @return ZfcUserLoginWidget
     */
    public function setViewTemplate($viewTemplate)
    {
        $this->viewTemplate = $viewTemplate;
        return $this;
    }
}
