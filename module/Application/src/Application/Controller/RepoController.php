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
    protected $repoList;

    protected $repository = null;

    protected $moduleService;

    public function shareAction()
    {
        $request = $this->getRequest();
        if($request->isPost()) {
            $repository = $request->getPost()->get('repository');
            $this->fetchUserRepositories();
            $repository = $this->getRepository($repository);
            
            if($repository) {
                $service = $this->getModuleService();
                $module = $service->register($repository);
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
        $repository = null;
        foreach($this->repoList as $repo) {
            if($repo->htmlUrl == $repositoryUrl) {
                $repository = $repo;
                break;
            }
        }
        
        return $repository;
    }

    public function fetchUserRepositories()
    {
        $sm = $this->getServiceLocator();

        $api = $sm->get('edpgithub_api_factory');

        $repoList = array();
        $service = $api->getService('Repo');
        $memberRepositories = $service->listRepositories(null, 'member');
       
        foreach($memberRepositories as $repo) {
            $repoList[$repo->htmlUrl] = $repo;
        }
        $allRepositories = $service->listRepositories(null, 'all');
       
        foreach($allRepositories as $repo) {
            if(!$repo->getFork()) {
                $repoList[$repo->htmlUrl] = $repo;
            }
        }

        $this->repoList = $repoList;
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
