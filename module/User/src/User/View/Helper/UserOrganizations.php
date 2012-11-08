<?php

namespace User\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserOrganizations extends AbstractHelper implements ServiceLocatorAwareInterface
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
        $client = $sl->get('EdpGithub\Client');

        $orgs = $client->api('current_user')->orgs();
        $vm = new ViewModel(array(
            'orgs' => $orgs
        ));
        $vm->setTemplate('helper/user-organizations.phtml');

        return $this->getView()->render($vm);
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
