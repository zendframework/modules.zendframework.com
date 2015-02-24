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
                'name' => 'Information',
                'class' => 'alert alert-info',
            ],
            PluginFlashMessenger::NAMESPACE_ERROR => [
                'name' => 'Error',
                'class' => 'alert alert-danger',
            ],
            PluginFlashMessenger::NAMESPACE_SUCCESS => [
                'name' => 'Success',
                'class' => 'alert alert-success',
            ],
            PluginFlashMessenger::NAMESPACE_DEFAULT => [
                'name' => 'Message',
                'class' => 'alert alert-info',
            ],
            PluginFlashMessenger::NAMESPACE_WARNING => [
                'name' => 'Warning',
                'class' => 'alert alert-warning',
            ],
        ];

        // if custom namespace handle as default message
        if (!isset($this->classOptions[$namespace])) {
            $this->classOptions[$namespace] = $this->classOptions[PluginFlashMessenger::NAMESPACE_DEFAULT];
        }

        $messageOutput = '';
        $translator = $this->getTranslator();

        foreach ($this->classOptions as $currentNamespace => $options) {
            $this->classMessages[$currentNamespace] = $options['class'];
            $openingString = sprintf('<div%%s><span class="sr-only">%s</span>', $translator->translate($options['name']));

            $this->setMessageOpenFormat($openingString);
            $this->setMessageSeparatorString(sprintf('</div>%s', $openingString));
            $this->setMessageCloseString('</div>');

            $messageOutput .= parent::render($currentNamespace, $classes);
        }

        return $messageOutput;
    }
}
