<?php

namespace Application\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SanitizeHtmlFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return SanitizeHtml
     */
    public function createService(ServiceLocatorInterface $pluginManager)
    {
        /** @var \Zend\View\HelperPluginManager $pluginManager */
        $serviceLocator = $pluginManager->getServiceLocator();
        $htmlPurifier = $serviceLocator->get(\HTMLPurifier::class);

        return new SanitizeHtml($htmlPurifier);
    }
}
