<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ComposerView extends AbstractHelper
{
    /**
     * @param string $composerConf
     * @return string
     */
    public function __invoke($composerConf)
    {
        return $this->getView()->render('zf-module/helper/composer-view.phtml', [
            'composerConf' => json_decode($composerConf, true),
        ]);
    }
}
