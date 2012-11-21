<?php

namespace ZfModule\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    protected $moduleService;

    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $owner = $this->params()->fromRoute('owner', null);

        $sm = $this->getServiceLocator();
        $client = $sm->get('EdpGithub\Client');
        $mapper = $sm->get('zfmodule_mapper_module');

        $repositories = array();

        if($owner) {
            $repos = $client->api('user')->repos($owner);
        } else {
            $repos = $client->api('current_user')->repos(array('type' =>'all', 'per_page' => 100));
        }

        foreach($repos as $repo) {
            if(!$repo->fork && $repo->permissions->push) {
                $module = $mapper->findByName($repo->name);
                if(!$module && $this->getModuleService()->isModule($repo)) {
                   $repositories[] = $repo;
                }
            }
        }
        $viewModel = new ViewModel(array('repositories' => $repositories));
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    /**
     * This function is used to submit a module from the site
     * @throws Exception\UnexpectedValueException
     * @return
     **/
    public function addAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $request = $this->getRequest();
        if($request->isPost()) {
            $repo = $request->getPost()->get('repo');
            $owner  = $request->getPost()->get('owner');

            $sm = $this->getServiceLocator();
            $repository = $sm->get('EdpGithub\Client')->api('repos')->show($owner, $repo);
            $repository = json_decode($repository);

            if(!($repository instanceOf \stdClass)) {
                throw new Exception\RuntimeException(
                    'Not able to fetch the repository from github due to an unknown error.',
                    500
                );
            }

            $service = $this->getModuleService();
            if(!$repository->fork && $repository->permissions->push) {
                if($service->isModule($repository)) {
                    $module = $service->register($repository);
                    $this->flashMessenger()->addMessage($module->getName() .' has been added to ZF Modules');
                } else {
                    throw new Exception\UnexpectedValueException(
                        $repository->name . ' is not a Zend Framework Module',
                        403
                    );
                }
            }else {
                throw new Exception\UnexpectedValueException(
                    'You have no permission to add this module. The reason might be that you are' .
                    'neither the owner nor a collaborator of this repository.',
                    403
                );
            }
        } else {
            throw new Exception\UnexpectedValueException(
                'Something went wrong with the post values of the request...'
            );
        }

       return $this->redirect()->toRoute('zfcuser');
    }

    /**
     * This function is used to remove a module from the site
     * @throws Exception\UnexpectedValueException
     * @return
     **/
    public function removeAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $request = $this->getRequest();
        if($request->isPost()) {
            $repo = $request->getPost()->get('repo');
            $owner  = $request->getPost()->get('owner');

            $sm = $this->getServiceLocator();
            $repository = $sm->get('EdpGithub\Client')->api('repos')->show($owner, $repo);
            $repository = json_decode($repository);

            if(!$repository instanceOf \stdClass) {
                throw new Exception\RuntimeException(
                    'Not able to fetch the repository from github due to an unknown error.',
                    500
                );
            }

            if(!$repository->fork && $repository->permissions->push) {
                $mapper = $sm->get('zfmodule_mapper_module');
                $module = $mapper->findByUrl($repository->html_url);
                if($module instanceOf \ZfModule\Entity\Module) {
                    $module = $mapper->delete($module);
                    $this->flashMessenger()->addMessage($repository->name .' has been removed from ZF Modules');
                } else {
                    throw new Exception\UnexpectedValueException(
                        $repository->name . ' was not found',
                        403
                    );
                }
            }else {
                throw new Exception\UnexpectedValueException(
                    'You have no permission to add this module. The reason might be that you are' .
                    'neither the owner nor a collaborator of this repository.',
                    403
                );
            }
        } else {
            throw new Exception\UnexpectedValueException(
                'Something went wrong with the post values of the request...'
            );
        }

       return $this->redirect()->toRoute('zfcuser');
    }

    /**
     * Getters/setters for DI stuff
     */
    public function getModuleService()
    {
        if (!$this->moduleService) {
            $this->moduleService = $this->getServiceLocator()->get('zfmodule_service_module');
        }
        return $this->moduleService;
    }

    public function setModuleService($moduleService)
    {
        $this->moduleService = $moduleService;
    }
}
