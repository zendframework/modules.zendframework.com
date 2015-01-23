<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
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

        //return $modules;
        $vm = new ViewModel([
            'modules' => $modules,
        ]);
        $vm->setTemplate('zf-module/helper/new-module');

        return $this->getView()->render($vm);
    }
}
