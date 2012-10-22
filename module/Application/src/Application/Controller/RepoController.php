<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RepoController extends AbstractActionController
{
    protected $repository = null;

    protected $moduleService;

    public function addAction()
    {
        $request = $this->getRequest();
        if($request->isPost()) {
            $repository = $request->getPost()->get('repository');
            $repository = $this->getRepository($repository);
            if($repository) {
                $service = $this->getModuleService();
                $module = $service->register($repository);
                $this->flashMessenger()->addMessage($module->getName() .' has been added to ZF Modules');
            } else {
                echo 'no permission.. another cool error message with an awesome exit';
                exit;
            }
        } else {
            echo "wrong values blah... need to change this";
            exit;
        }
       return $this->redirect()->toRoute('zfcuser');
    }

    public function removeAction()
    {
        $request = $this->getRequest();
        if($request->isPost()) {
            $repository = $request->getPost()->get('repository');
            $repository = $this->getRepository($repository);
            if($repository) {
                $sm = $this->getServiceLocator();
                $mapper = $sm->get('application_module_mapper');
                $module = $mapper->findByUrl($repository->getHtmlUrl());
                $module = $mapper->delete($module);
                $this->flashMessenger()->addMessage($repository->getName() . ' has been removed from ZF Modules');
            } else {
                echo 'no permission.. another cool error message with an awesome exit';
                exit;
            }
        } else {
            echo "wrong values blah... need to change this";
            exit;
        }
       return $this->redirect()->toRoute('zfcuser');

    }

    /**
     * Return Repository
     * 
     * @param  string $repository 
     * @return EdpGithub\ApiClient\Model\Repo
     */
    public function getRepository($repositoryUrl)
    {
        $sm = $this->getServiceLocator();
        $api = $sm->get('edpgithub_api_factory');
        $service = $api->getService('Repo');
        $repositories = $service->listRepositories(null, 'all');

        $repository = null;
        foreach($repositories as $repo) {
            if($repo->getHtmlUrl() == $repositoryUrl) {
                if(!$repo->getFork()) {
                    $repository = $repo;
                }
                return $repository;
            } 
        }

        return $repository;
    }

    /**
     * Getters/setters for DI stuff
     */

    public function getModuleService()
    {
        if (!$this->moduleService) {
            $this->moduleService = $this->getServiceLocator()->get('application_module_service');
        }
        return $this->moduleService;
    }

    public function setModuleService($moduleService) 
    {
        $this->moduleService = $moduleService;
    }
}
