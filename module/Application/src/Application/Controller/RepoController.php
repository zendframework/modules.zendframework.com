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
     * This function is used to submit a module from the site
     * @throws Exception\UnexpectedValueException
     * @return
     **/
    public function addAction()
    {
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

            if(!$repository->fork && $repository->permissions->push) {
                if($this->isModule($repository)) {
                    $service = $this->getModuleService();
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
                $mapper = $sm->get('application_module_mapper');
                $module = $mapper->findByUrl($repository->html_url);
                if($module instanceOf \Application\Entity\Module) {
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
     * Check if Repo is a ZF Module
     * @param  array  $repo
     * @return boolean
     */
    public function isModule($repo)
    {
        $sm = $this->getServiceLocator();
        $client = $sm->get('EdpGithub\Client');
        try{
            $module = $client->api('repos')->content($repo->owner->login, $repo->name, 'Module.php');
        } catch(\Exception $e) {
            return false;
        }

        if(!json_decode($module) instanceOf \stdClass) {
            return false;
        }

        return true;
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
