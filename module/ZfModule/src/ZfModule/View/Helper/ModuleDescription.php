<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ModuleDescription extends AbstractHelper
{
    /**
     * @param $repository
     * @return string
     */
    public function __invoke($repository)
    {
        return $this->getView()->render('zf-module/helper/module-description.phtml', [
            'owner' => $repository->owner->login,
            'name' => $repository->name,
            'createdAt' => $repository->created_at,
            'url' => $repository->html_url,
            'photoUrl' => $repository->owner->avatar_url,
            'description' => $repository->description,
        ]);
    }
}
