<?php

namespace User\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class UserOrganizations extends AbstractHelper implements ServiceManagerAwareInterface
{
    /**
     * $var string template used for view
     */
    protected $viewTemplate;

        /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * __invoke
     *
     * @access public
     * @return string
     */
    public function __invoke()
    {
        $sm = $this->getServiceManager();

        //need to fetch top lvl ServiceManager
        $sm = $sm->getServiceLocator();
        $client = $sm->get('EdpGithub\Client');

        $orgs = $client->api('current_user')->orgs();
        $vm = new ViewModel(array(
            'orgs' => $orgs
        ));
        $vm->setTemplate('helper/user-organizations.phtml');

        return $this->getView()->render($vm);
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
