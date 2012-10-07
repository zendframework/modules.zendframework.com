<?php
namespace Assetic;

class Module {
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/assetic/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
