<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NewModules extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate;

    /**
     * @var ServiceLocator
     */
    protected $serviceLocator;

    /**
     * __invoke
     *
     * @access public
     * @param array $options array of options
     * @return array Array of modules
     */
    public function __invoke($options = null)
    {
        $sl = $this->getServiceLocator();

        //need to fetch top lvl serviceLocator
        $sl = $sl->getServiceLocator();

        $mapper = $sl->get('application_module_mapper');
        $modules = $mapper->findAll(10, 'created_at', 'DESC');

        //return $modules;
        $vm = new ViewModel(array(
            'modules' => $modules,
        ));
        $vm->setTemplate('application/helper/new-modules.phtml');

        return $this->getView()->render($vm);
    }

    /**
     * @param string $viewTemplate
     * @return NewModules
     */
    public function setViewTemplate($viewTemplate)
    {
        $this->viewTemplate = $viewTemplate;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
