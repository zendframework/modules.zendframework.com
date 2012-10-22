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

    /**
     * This function is used to submit a module to the site
     * @throws Exception\UnexpectedValueException
     * @return 
     **/
    public function addAction()
    {
        $repository = $this->getRepository();
        if($repository) {
            $service = $this->getModuleService();
            $module = $service->register($repository);
            $this->flashMessenger()->addMessage($module->getName() .' has been added to ZF Modules');
        } else {
            throw new Exception\UnexpectedValueException(
                'You have no permission to add this module. The reason might be that you are' .
                'neither the owner nor a collaborator of this repository.',
                403
            );
        }
       return $this->redirect()->toRoute('zfcuser');
    }

    /**
     * This function is used to remove a module to the site
     * @throws Exception\UnexpectedValueException
     * @return 
     **/
   public function removeAction()
    {
        $repository = $this->getRepository();
        if($repository) {
            $sm = $this->getServiceLocator();
            $mapper = $sm->get('application_module_mapper');
            $module = $mapper->findByUrl($repository->getHtmlUrl());
            $module = $mapper->delete($module);
            $this->flashMessenger()->addMessage($repository->getName() . ' has been removed from ZF Modules');
        } else {
            throw new Exception\UnexpectedValueException(
                'You have no permission to add this module. The reason might be that you are' .
                'neither the owner nor a collaborator of this repository.',
                403
            );
        }
       return $this->redirect()->toRoute('zfcuser');
    }

    /**
     * Return Repository
     * @throws Exception\UnexpectedValueException
     * @return EdpGithub\ApiClient\Model\Repo
     */
    public function getRepository()
    {
        $request = $this->getRequest();
        if($request->isPost()) {
            $repositoryUrl = $request->getPost()->get('repository');
            
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
        throw new Exception\UnexpectedValueException(
            'Something went wrong with the post values of the request...'
        );
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
