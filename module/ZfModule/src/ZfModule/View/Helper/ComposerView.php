<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class ComposerView extends AbstractHelper
{
    /**
     * @param string $composerConf
     * @return string
     */
    public function __invoke($composerConf)
    {
        $vm = new ViewModel([
            'composerConf' => json_decode($composerConf, true),
        ]);
        $vm->setTemplate('zf-module/helper/composer-view.phtml');

        return $this->getView()->render($vm);
    }
}
