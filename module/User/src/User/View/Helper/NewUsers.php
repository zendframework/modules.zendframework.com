<?php

namespace User\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NewUsers extends AbstractHelper implements ServiceLocatorAwareInterface
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
     * @return string
     */
    public function __invoke()
    {
        $sl = $this->getServiceLocator();

        //need to fetch top lvl ServiceLocator
        $sl = $sl->getServiceLocator();
        $mapper = $sl->get('zfcuser_user_mapper');
        $users = $mapper->findAll(16, 'created_at', 'DESC');

        $vm = new ViewModel(array(
            'users' => $users,
        ));
        $vm->setTemplate('user/helper/new-users');

        return $this->getView()->render($vm);
    }

    /**
     * @param string $viewTemplate
     * @return NewUsers
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
