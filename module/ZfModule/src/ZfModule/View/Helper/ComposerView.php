<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class ComposerView extends AbstractHelper
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
    public function __invoke($composerConf)
    {
        $vm = new ViewModel(array(
            'composerConf' => $composerConf,
        ));
        $vm->setTemplate('zf-module/helper/composer-view.phtml');


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
