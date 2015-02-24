<?php
namespace Application\View\Helper;

use Zend\Mvc\Controller\Plugin\FlashMessenger as PluginFlashMessenger;
use Zend\View\Helper\FlashMessenger as ZendFlashMessenger;

class FlashMessenger extends ZendFlashMessenger
{
    /**
     * Render Messages
     *
     * @param  string $namespace
     * @param  array  $classes
     * @return string
     */
    public function render($namespace = PluginFlashMessenger::NAMESPACE_DEFAULT, array $classes = array())
    {
        // set classes
        $this->classMessages = array(
            'error' 	=> 'alert alert-dismissable alert-danger',
            'default' 	=> 'alert alert-dismissable alert-warning',
            'info' 		=> 'alert alert-dismissable alert-info',
            'success'	=> 'alert alert-dismissable alert-success',
        );

        $this->setMessageSeparatorString('</li><li>');
        $this->setMessageCloseString('</li></ul></div>');

        $this->setMessageOpenFormat(
            sprintf('<div%%s><h4>%s</h4><ul><li>', $this->getTranslator()->translate('Warning', $this->getTranslatorTextDomain()))
        );
        $default = parent::render(PluginFlashMessenger::NAMESPACE_DEFAULT);

        $this->setMessageOpenFormat(
            sprintf('<div%%s><h4>%s</h4><ul><li>', $this->getTranslator()->translate('Error', $this->getTranslatorTextDomain()))
        );
        $error = parent::render(PluginFlashMessenger::NAMESPACE_ERROR);

        $this->setMessageOpenFormat(
            sprintf('<div%%s><h4>%s</h4><ul><li>', $this->getTranslator()->translate('Information', $this->getTranslatorTextDomain()))
        );
        $info = parent::render(PluginFlashMessenger::NAMESPACE_INFO);

        $this->setMessageOpenFormat(
            sprintf('<div%%s><h4>%s</h4><ul><li>', $this->getTranslator()->translate('Success', $this->getTranslatorTextDomain()))
        );
        $success = parent::render(PluginFlashMessenger::NAMESPACE_SUCCESS);

        return $success . $error . $default . $info;
    }
}
