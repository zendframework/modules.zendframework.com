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
            $module = $mapper->findByUrl($repository->html_url);
            $module = $mapper->delete($module);
            $this->flashMessenger()->addMessage($repository->name . ' has been removed from ZF Modules');
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
     * @todo get information for single repository
     */
    public function getRepository()
    {
        $request = $this->getRequest();
        if($request->isPost()) {
            $repositoryUrl = $request->getPost()->get('repository');

            $sm = $this->getServiceLocator();
            $repositories = $sm->get('EdpGithub\Client')->api('current_user')->repos();

            $repository = null;
            foreach($repositories as $repo) {
                if($repo->html_url == $repositoryUrl) {
                    if(!$repo->fork) {
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

    public function userReposAction()
    {
        $sm = $this->getServiceLocator();
        $client = $sm->get('EdpGithub\Client');

        $repositories = array();

        $ownerRepos = $client->api('current_user')->repos(array('type' =>'owner'));
        foreach($ownerRepos as $repo) {
            if(!$repo->fork) {
                $repositories[] = $repo;
            }
        }

        $memberRepos = $client->api('current_user')->repos(array('type' =>'member'));
        foreach($memberRepos as $repo) {
            $repositories[] = $repo;
        }

        $mapper = $sm->get('application_module_mapper');
        foreach($repositories as $key => $repo) {
            if($repo->fork) {
                unset($repositories[$key]);
            } else {
                $module = $mapper->findByName($repo->name);
                if($module) {
                    unset($repositories[$key]);
                } else {
                    $em = $client->getHttpClient()->getEventManager();
                    $errorListener = $sm->get('EdpGithub\Listener\Error');
                    $em->detachAggregate($errorListener);
                    $module = $client->api('repos')->content($repo->full_name, 'Module.php');
                    $response = $client->getHttpClient()->getResponse();
                    if(!$response->isSuccess()){
                        unset($repositories[$key]);
                    }
                    $em->attachAggregate($errorListener);
                }
            }
        }

        $viewModel = new ViewModel(array('repositories' => $repositories));
        $viewModel->setTerminal(true);
        return $viewModel;
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
