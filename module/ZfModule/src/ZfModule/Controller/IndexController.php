<?php

namespace ZfModule\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    protected $moduleService;

    public function viewAction()
    {
        $vendor = $this->params()->fromRoute('vendor', null);
        $module = $this->params()->fromRoute('module', null);

        $sl = $this->getServiceLocator();
        $mapper = $sl->get('zfmodule_mapper_module');

        //check if module is existing in database otherwise return 404 page
        $result = $mapper->findByName($module);
        if(!$result) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $client = $sl->get('EdpGithub\Client');
        /* @var $cache StorageInterface */
        $cache = $sl->get('zfmodule_cache');

        $cacheKey = 'module-view-' . $vendor . '-' . $module;

        $repository = json_decode($client->api('repos')->show($vendor, $module));
        $httpClient = $client->getHttpClient();
        $response= $httpClient->getResponse();
        if($response->getStatusCode() == 304 && $cache->hasItem($cacheKey)) {
            return $cache->getItem($cacheKey);
        }

        $readme = $client->api('repos')->readme($vendor, $module);
        $readme = json_decode($readme);
        $repository = json_decode($client->api('repos')->show($vendor, $module));

        try{
            $license = $client->api('repos')->content($vendor, $module, 'LICENSE');
            $license = json_decode($license);
            $license = base64_decode($license->content);
        } catch(\Exception $e) {
            $license = 'No license file found for this Module';
        }


        $viewModel = new ViewModel(array(
            'vendor' => $vendor,
            'module' => $module,
            'repository' => $repository,
            'readme' => base64_decode($readme->content),
            'license' => $license,
        ));

        $cache->setItem($cacheKey , $viewModel);

        return $viewModel;
    }

    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $sl = $this->getServiceLocator();
        $client = $sl->get('EdpGithub\Client');

        $repos = $client->api('current_user')->repos(array('type' =>'all', 'per_page' => 100));

        $identity = $this->zfcUserAuthentication()->getIdentity();
        $cacheKey = 'modules-user-' . $identity->getId();

        $repositories = $this->fetchModules($repos, $cacheKey);

        $viewModel = new ViewModel(array('repositories' => $repositories));
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function organizationAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $sl = $this->getServiceLocator();
        $client = $sl->get('EdpGithub\Client');

        $owner = $this->params()->fromRoute('owner', null);
        $repos = $client->api('user')->repos($owner);

        $identity = $this->zfcUserAuthentication()->getIdentity();
        $cacheKey = 'modules-organization-' . $identity->getId() . '-' . $owner;

        $repositories = $this->fetchModules($repos, $cacheKey);
        $viewModel = new ViewModel(array('repositories' => $repositories));
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('zf-module/index/index.phtml');
        return $viewModel;
    }

    public function fetchModules($repos, $cacheKey)
    {
        $sl = $this->getServiceLocator();
        $mapper = $sl->get('zfmodule_mapper_module');
        $client = $sl->get('EdpGithub\Client');
        /* @var $cache StorageInterface */
        $cache = $sl->get('zfmodule_cache');

        $repositories = array();
        //fetch only modules from github
        foreach($repos as $repo) {
            //Need to see first if any repository has been updated
            $httpClient = $client->getHttpClient();
            $response= $httpClient->getResponse();
            if($response->getStatusCode() == 304) {
                if($cache->hasItem($cacheKey . '-github')) {
                    $repositories =  $cache->getItem($cacheKey . '-github');
                    break;
                }
            }
            if(!$repo->fork && $repo->permissions->push) {
                if($this->getModuleService()->isModule($repo)) {
                   $repositories[] = $repo;
                }
            }
        }
        //save list of modules to cache
        if(!$cache->hasItem($cacheKey . '-github')) {
            $cache->setItem($cacheKey . '-github', $repositories);
        }

        //check if cache for modules exist
        if(!$cache->hasItem($cacheKey)) {
            //check if module is in database
            foreach($repositories as $key => $repo) {
                $module = $mapper->findByName($repo->name);
                if($module) {
                    unset($repositories[$key]);
                }
            }
            //save database mapped list to cache
            $cache->setItem($cacheKey , $repositories);
            // create cache tags
            $identity = $this->zfcUserAuthentication()->getIdentity();

            $tags = array($identity->getUsername() . '-' . $identity->getId());
            $cache->setTags($cacheKey, $tags);
        } else {
            $repositories = $cache->getItem($cacheKey);
        }

        return $repositories;
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

        $this->clearModuleCache();

        return $this->redirect()->toRoute('zfcuser');
    }

    public function clearModuleCache()
    {
        $sl = $this->getServiceLocator();
        $cache = $sl->get('zfmodule_cache');
        $identity = $this->zfcUserAuthentication()->getIdentity();

        $tags = array($identity->getUsername() . '-' . $identity->getId());
        $cache->clearByTags($tags);
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

        $this->clearModuleCache();
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
