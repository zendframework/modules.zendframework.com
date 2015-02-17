<?php

namespace ZfModule\View\Helper;

use stdClass;
use Zend\View\Helper\AbstractHelper;

class ModuleView extends AbstractHelper
{
    const BUTTON_SUBMIT = 'submit';
    const BUTTON_REMOVE = 'remove';

    /**
     * @param stdClass $module
     * @param string $button
     * @return string
     */
    public function __invoke($module, $button = 'submit')
    {
        return $this->getView()->render('zf-module/helper/module-view.phtml', [
            'owner' => $module->owner->login,
            'name' => $module->name,
            'createdAt' => $module->created_at,
            'url' => $module->html_url,
            'photoUrl' => $module->owner->avatar_url,
            'description' => $module->description,
            'button' => $button,
        ]);
    }
}
