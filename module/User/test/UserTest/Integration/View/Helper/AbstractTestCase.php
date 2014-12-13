<?php

namespace UserTest\Integration\View\Helper;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\View\HelperPluginManager;

abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @return ServiceManager
     */
    protected function getServiceManager()
    {
        return Bootstrap::getServiceManager();
    }

    /**
     * @return HelperPluginManager
     */
    protected function getHelperPluginManager()
    {
        return $this->getServiceManager()->get('ViewHelperManager');
    }
}
