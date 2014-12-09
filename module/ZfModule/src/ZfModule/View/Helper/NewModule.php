<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use ZfModule\Mapper\Module;

class NewModule extends AbstractHelper
{
    /** @var Module */
    protected $moduleMapper;

    /**
     * Constructor
     *
     * @param Module $moduleMapper
     */
    public function __construct(Module $moduleMapper)
    {
        $this->moduleMapper = $moduleMapper;
    }

    /**
     * Return Module Db Mapper
     *
     * @return Module
     */
    public function getModuleMapper()
    {
        return $this->moduleMapper;
    }

    /**
     * __invoke
     *
     * @access public
     * @param array $options array of options
     * @return array Array of modules
     */
    public function __invoke($options = null)
    {
        $modules = $this->getModuleMapper()->findAll(10, 'created_at', 'DESC');

        //return $modules;
        $vm = new ViewModel(array(
            'modules' => $modules,
        ));
        $vm->setTemplate('zf-module/helper/new-module');

        return $this->getView()->render($vm);
    }
}
