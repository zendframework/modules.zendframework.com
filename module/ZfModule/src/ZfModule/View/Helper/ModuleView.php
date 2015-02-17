<?php

namespace ZfModule\View\Helper;

use InvalidArgumentException;
use stdClass;
use Zend\View\Helper\AbstractHelper;
use ZfModule\Entity;

class ModuleView extends AbstractHelper
{
    const BUTTON_SUBMIT = 'submit';
    const BUTTON_REMOVE = 'remove';

    /**
     * @param Entity\Module|stdClass $module
     * @param string $button
     * @return string
     */
    public function __invoke($module, $button = 'submit')
    {
        $values = $this->fetchValues($module);

        $values += [
            'button' => $button,
        ];

        return $this->getView()->render('zf-module/helper/module-view.phtml', $values);
    }

    /**
     * @param stdClass|Entity\Module$module
     * @return array
     */
    private function fetchValues($module)
    {
        if ((!$module instanceof stdClass) && !($module instanceof Entity\Module)) {
            throw new InvalidArgumentException(sprintf(
                'Parameter "%s" needs to be specified as %s',
                '$module',
                'an instance of stdClass or ZfModule\Entity\Module'
            ));
        }

        if ($module instanceof stdClass) {
            return [
                'owner' => $module->owner->login,
                'name' => $module->name,
                'createdAt' => $module->created_at,
                'url' => $module->html_url,
                'photoUrl' => $module->owner->avatar_url,
                'description' => $module->description,
            ];
        }

        return [
            'owner' => $module->getOwner(),
            'name' => $module->getName(),
            'createdAt' => $module->getCreatedAt(),
            'url' => $module->getUrl(),
            'photoUrl' => $module->getPhotoUrl(),
            'description' => $module->getDescription(),
        ];
    }
}
