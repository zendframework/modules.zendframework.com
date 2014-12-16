<?php

namespace ZfModule\Controller;

use Application\Service\GithubService;
use EdpGithub\Client;
use EdpGithub\Collection\RepositoryCollection;
use Zend\Cache;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfModule\Mapper;
use ZfModule\Service;

class IndexController extends AbstractActionController
{
    /**
     * @var Cache\Storage\StorageInterface
     */
    private $moduleCache;

    /**
     * @var Mapper\Module
     */
    private $moduleMapper;

    /**
     * @var Service\Module
     */
    private $moduleService;

    /**
     * @var Client
     */
    private $githubClient;

    /**
     * @var GithubService
     */
    private $githubService;

    /**
     * @param Cache\Storage\StorageInterface $moduleCache
     * @param Mapper\Module $moduleMapper
     * @param Service\Module $moduleService
     * @param Client $githubClient
     * @param GithubService $githubService
     */
    public function __construct(
        Cache\Storage\StorageInterface $moduleCache,
        Mapper\Module $moduleMapper,
        Service\Module $moduleService,
        Client $githubClient,
        GithubService $githubService
    ) {
        $this->moduleCache = $moduleCache;
        $this->moduleMapper = $moduleMapper;
        $this->moduleService = $moduleService;
        $this->githubClient = $githubClient;
        $this->githubService = $githubService;
    }

    public function viewAction()
    {
        $vendor = $this->params()->fromRoute('vendor', null);
        $module = $this->params()->fromRoute('module', null);

        $result = $this->moduleMapper->findByName($module);
        if (!$result) {
            $this->getResponse()->setStatusCode(Http\Response::STATUS_CODE_404);
            return;
        }

        $repositoryCacheKey = 'module-view-' . $vendor . '-' . $module;
        $repository = $this->githubService->getUserRepositoryMetadata($vendor, $module);

        $httpClient = $this->githubClient->getHttpClient();
        $response= $httpClient->getResponse();
        if ($response->getStatusCode() == Http\Response::STATUS_CODE_304 && $this->moduleCache->hasItem($repositoryCacheKey)) {
            return $this->moduleCache->getItem($repositoryCacheKey);
        }

        $readme = $this->githubService->getRepositoryFileContent($vendor, $module, 'README.md');
        $license = $this->githubService->getRepositoryFileContent($vendor, $module, 'LICENSE');
        $license = $license === false ? 'No license file found for this Module' : $license;

        $composerConf = $this->githubService->getRepositoryFileContent($vendor, $module, 'composer.json');
        $composerConf = $composerConf === false ? 'No composer.json file found for this Module' : json_decode($composerConf, true);

        $viewModel = new ViewModel(array(
            'vendor' => $vendor,
            'module' => $module,
            'repository' => $repository,
            'readme' => $readme,
            'composerConf' => $composerConf,
            'license' => $license,
        ));

        $this->moduleCache->setItem($repositoryCacheKey, $viewModel);

        return $viewModel;
    }

    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $params = array(
            'type'      => 'all',
            'per_page'  => 100,
            'sort'      => 'updated',
            'direction' => 'desc',
        );

        /* @var RepositoryCollection $repos */
        $repos = $this->githubService->getAuthUserRepositories($params);

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

        $owner = $this->params()->fromRoute('owner', null);
        $params = array(
            'per_page'  => 100,
            'sort'      => 'updated',
            'direction' => 'desc',
        );

        /* @var RepositoryCollection $repos */
        $repos = $this->githubService->getUserRepositories($owner, $params);

        $identity = $this->zfcUserAuthentication()->getIdentity();
        $cacheKey = 'modules-organization-' . $identity->getId() . '-' . $owner;

        $repositories = $this->fetchModules($repos, $cacheKey);
        $viewModel = new ViewModel(array('repositories' => $repositories));
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('zf-module/index/index.phtml');
        return $viewModel;
    }

    /**
     * @param RepositoryCollection $repos
     * @param string $cacheKey
     * @return array
     */
    public function fetchModules(RepositoryCollection $repos, $cacheKey)
    {
        $cacheKey .= '-github';

        $repositories = array();

        foreach ($repos as $repo) {
            $isModule = $this->moduleService->isModule($repo);
            //Verify if repos have been modified
            $httpClient = $this->githubClient->getHttpClient();
            /* @var $response \Zend\Http\Response */
            $response = $httpClient->getResponse();

            $hasCache = $this->moduleCache->hasItem($cacheKey);

            if ($response->getStatusCode() == Http\Response::STATUS_CODE_304 && $hasCache) {
                $repositories = $this->moduleCache->getItem($cacheKey);
                break;
            }

            if (!$repo->fork && $repo->permissions->push && $isModule && !$this->moduleMapper->findByName($repo->name)) {
                $repositories[] = $repo;
                $this->moduleCache->removeItem($cacheKey);
            }
        }

        //save list of modules to cache
        $this->moduleCache->setItem($cacheKey, $repositories);

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
        if ($request->isPost()) {
            $repo = $request->getPost()->get('repo');
            $owner  = $request->getPost()->get('owner');

            $repository = $this->githubService->getUserRepositoryMetadata($owner, $repo);

            if (!($repository instanceof \stdClass)) {
                throw new Exception\RuntimeException(
                    'Not able to fetch the repository from github due to an unknown error.',
                    Http\Response::STATUS_CODE_500
                );
            }

            if (!$repository->fork && $repository->permissions->push) {
                if ($this->moduleService->isModule($repository)) {
                    $module = $this->moduleService->register($repository);
                    $this->flashMessenger()->addMessage($module->getName() .' has been added to ZF Modules');
                } else {
                    throw new Exception\UnexpectedValueException(
                        $repository->name . ' is not a Zend Framework Module',
                        Http\Response::STATUS_CODE_403
                    );
                }
            } else {
                throw new Exception\UnexpectedValueException(
                    'You have no permission to add this module. The reason might be that you are' .
                    'neither the owner nor a collaborator of this repository.',
                    Http\Response::STATUS_CODE_403
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
        $identity = $this->zfcUserAuthentication()->getIdentity();

        $tags = array($identity->getUsername() . '-' . $identity->getId());
        $this->moduleCache->clearByTags($tags);
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
        if ($request->isPost()) {
            $repo = $request->getPost()->get('repo');
            $owner  = $request->getPost()->get('owner');

            $repository = $this->githubService->getUserRepositoryMetadata($owner, $repo);

            if (!$repository instanceof \stdClass) {
                throw new Exception\RuntimeException(
                    'Not able to fetch the repository from github due to an unknown error.',
                    Http\Response::STATUS_CODE_500
                );
            }

            if (!$repository->fork && $repository->permissions->push) {
                $module = $this->moduleMapper->findByUrl($repository->html_url);
                if ($module instanceof \ZfModule\Entity\Module) {
                    $module = $this->moduleMapper->delete($module);
                    $this->flashMessenger()->addMessage($repository->name .' has been removed from ZF Modules');
                } else {
                    throw new Exception\UnexpectedValueException(
                        $repository->name . ' was not found',
                        Http\Response::STATUS_CODE_403
                    );
                }
            } else {
                throw new Exception\UnexpectedValueException(
                    'You have no permission to add this module. The reason might be that you are' .
                    'neither the owner nor a collaborator of this repository.',
                    Http\Response::STATUS_CODE_403
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
}
