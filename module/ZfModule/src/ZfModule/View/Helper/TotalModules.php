<?php

namespace ZfModule\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfModule\Mapper\Module;

class TotalModules extends AbstractHelper
{
    /** @var Module */
    private $moduleMapper;

    /**
     * @var int
     */
    protected $total;

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
    protected function getModuleMapper()
    {
        return $this->moduleMapper;
    }

    /**
     * __invoke
     *
     * @access public
     * @return string
     */
    public function __invoke()
    {
        if ($this->total === null) {
            $this->total = $this->getModuleMapper()->getTotal();
        }
        return $this->total;
    }
}
