<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfModule\Mapper;

class NewModule extends AbstractHelper
{
    /**
     * @var Mapper\Module
     */
    private $moduleMapper;

    /**
     * @param Mapper\Module $moduleMapper
     */
    public function __construct(Mapper\Module $moduleMapper)
    {
        $this->moduleMapper = $moduleMapper;
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        $modules = $this->moduleMapper->findAll(10, 'created_at', 'DESC');

        return $this->getView()->render('zf-module/helper/new-module', [
            'modules' => $modules,
        ]);
    }
}
