<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HtmlPurifierFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \HTMLPurifier
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        $options = [];
        if (isset($config['htmlpurifier'])) {
            $options = $config['htmlpurifier'];
        }

        return new \HTMLPurifier($options);
    }
}
