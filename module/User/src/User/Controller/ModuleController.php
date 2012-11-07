<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class ModuleController extends AbstractActionController
{
    public function orgsAction()
    {
        $org = $this->params()->fromRoute('org');

        $sm = $this->getServiceLocator();
        $client = $sm->get('EdpGithub\Client');
        $mapper = $sm->get('application_module_mapper');

        $repos = $client->api('user')->repos($org);
        $repositories = array();
        foreach($repos as $repo) {
            if(!$repo->fork) {
                $module = $mapper->findByName($repo->name);
                if(!$module && $this->isModule($repo)) {
                   $repositories[] = $repo;
                }
            }
        }

        $viewModel = new ViewModel(array('repositories' => $repositories));
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function reposAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $sm = $this->getServiceLocator();
        $client = $sm->get('EdpGithub\Client');
        $mapper = $sm->get('application_module_mapper');

        $repositories = array();

        $ownerRepos = $client->api('current_user')->repos(array('type' =>'all', 'per_page' => 100));
        foreach($ownerRepos as $repo) {
            if(!$repo->fork) {
                 $module = $mapper->findByName($repo->name);
                if(!$module && $this->isModule($repo)) {
                   $repositories[] = $repo;
                }
            }
        }
        $viewModel = new ViewModel(array('repositories' => $repositories));
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    /**
     * Check if Repo is a ZF Module
     * @param  array  $repo
     * @return boolean
     */
    public function isModule($repo)
    {
        $sm = $this->getServiceLocator();
        $client = $sm->get('EdpGithub\Client');
        $em = $client->getHttpClient()->getEventManager();
        $errorListener = $sm->get('EdpGithub\Listener\Error');
        $em->detachAggregate($errorListener);
        $module = $client->api('repos')->content($repo->owner->login, $repo->name, 'Module.php');
        $response = $client->getHttpClient()->getResponse();
        $em->attachAggregate($errorListener);
        if(!$response->isSuccess()){
            return false;
        }

        return true;
    }
}
