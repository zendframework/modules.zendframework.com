<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class ModuleController extends AbstractActionController
{
    public function organizationsAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $sm = $this->getServiceLocator();
        $client = $sm->get('EdpGithub\Client');

        $organizations = array();

        $orgs = $client->api('current_user')->orgs();
        $orgs = json_decode($orgs, true);
        echo "<pre>";
        print_r($orgs);
        exit;
        return new JsonModel($orgs);
    }
}
