<?php
namespace Application\View\Helper;

use Zend\Mvc\Controller\Plugin\FlashMessenger as PluginFlashMessenger;
use Zend\View\Helper\FlashMessenger as ZendFlashMessenger;

class FlashMessenger extends ZendFlashMessenger
{
    /** @var array */
    private $classOptions = [];

    /**
     * Render Messages
     *
     * @param  string $namespace
     * @param  array $classes
     * @return string
     */
    public function render($namespace = PluginFlashMessenger::NAMESPACE_DEFAULT, array $classes = [])
    {
        $this->classOptions = [
            PluginFlashMessenger::NAMESPACE_INFO => [
                'name' => $this->getTranslator()->translate('Information'),
                'class' => 'alert alert-info',
            ],
            PluginFlashMessenger::NAMESPACE_ERROR => [
                'name' => $this->getTranslator()->translate('Error'),
                'class' => 'alert alert-danger',
            ],
            PluginFlashMessenger::NAMESPACE_SUCCESS => [
                'name' => $this->getTranslator()->translate('Success'),
                'class' => 'alert alert-success',
            ],
            PluginFlashMessenger::NAMESPACE_DEFAULT => [
                'name' => $this->getTranslator()->translate('Message'),
                'class' => 'alert alert-info',
            ],
            PluginFlashMessenger::NAMESPACE_WARNING => [
                'name' => $this->getTranslator()->translate('Warning'),
                'class' => 'alert alert-warning',
            ],
        ];

        // if custom namespace handle as default message
        if (!isset($this->classOptions[$namespace])) {
            $this->classOptions[$namespace] = $this->classOptions[PluginFlashMessenger::NAMESPACE_DEFAULT];
        }

        $messageOutput = '';

        foreach ($this->classOptions as $currentNamespace => $options) {
            $this->classMessages[$currentNamespace] = $options['class'];
            $openingString = sprintf('<div%%s><span class="sr-only">%s</span>', $options['name']);

            $this->setMessageOpenFormat($openingString);
            $this->setMessageSeparatorString(sprintf('</div>%s', $openingString));
            $this->setMessageCloseString('</div>');

            $messageOutput .= parent::render($currentNamespace, $classes);
        }

        return $messageOutput;
    }
}
