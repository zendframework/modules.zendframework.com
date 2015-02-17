<?php

namespace ZfModule\Controller;

use Application\Service\RepositoryRetriever;
use EdpGithub\Collection\RepositoryCollection;
use stdClass;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcUser\Controller\Plugin;
use ZfModule\Mapper;
use ZfModule\Service;

/**
 * @method Http\Request getRequest()
 * @method Plugin\ZfcUserAuthentication zfcUserAuthentication()
 */
class IndexController extends AbstractActionController
{
    /**
     * @var Mapper\Module
     */
    private $moduleMapper;

    /**
     * @var Service\Module
     */
    private $moduleService;

    /**
     * @var RepositoryRetriever
     */
    private $repositoryRetriever;

    /**
     * @param Mapper\Module $moduleMapper
     * @param Service\Module $moduleService
     * @param RepositoryRetriever $repositoryRetriever
     */
    public function __construct(
        Mapper\Module $moduleMapper,
        Service\Module $moduleService,
        RepositoryRetriever $repositoryRetriever
    ) {
        $this->moduleMapper = $moduleMapper;
        $this->moduleService = $moduleService;
        $this->repositoryRetriever = $repositoryRetriever;
    }

    public function viewAction()
    {
        $vendor = $this->params()->fromRoute('vendor', null);
        $module = $this->params()->fromRoute('module', null);

        $result = $this->moduleMapper->findByName($module);
        if (!$result) {
            return $this->notFoundAction();
        }

        $repository = $this->repositoryRetriever->getUserRepositoryMetadata($vendor, $module);
        if (!$repository) {
            return $this->notFoundAction();
        }

        $license = $this->repositoryRetriever->getRepositoryFileContent($vendor, $module, 'LICENSE');
        $composerConf = $this->repositoryRetriever->getRepositoryFileContent($vendor, $module, 'composer.json');

        /* HOTFIX for https://github.com/EvanDotPro/EdpGithub/issues/23 - markdown needs to be the last request */
        $readme = $this->repositoryRetriever->getRepositoryFileContent($vendor, $module, 'README.md', true);

        return new ViewModel([
            'vendor' => $vendor,
            'module' => $module,
            'repository' => $repository,
            'readme' => $readme,
            'composerConf' => $composerConf,
            'license' => $license,
        ]);
    }

    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $currentUserRepositories = $this->repositoryRetriever->getAuthenticatedUserRepositories([
            'type' => 'all',
            'per_page' => 100,
            'sort' => 'updated',
            'direction' => 'desc',
        ]);

        $repositories = $this->registeredRepositories($currentUserRepositories);

        $viewModel = new ViewModel(['repositories' => $repositories]);
        $viewModel->setTerminal(true);

        return $viewModel;
    }

    public function organizationAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $owner = $this->params()->fromRoute('owner', null);

        $userRepositories = $this->repositoryRetriever->getUserRepositories($owner, [
            'per_page' => 100,
            'sort' => 'updated',
            'direction' => 'desc',
        ]);

        $repositories = $this->registeredRepositories($userRepositories);

        $viewModel = new ViewModel(['repositories' => $repositories]);
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('zf-module/index/index.phtml');

        return $viewModel;
    }

    /**
     * @param RepositoryCollection $repositories
     * @return stdClass[]
     */
    private function registeredRepositories(RepositoryCollection $repositories)
    {
        return array_filter(iterator_to_array($repositories), function ($repository) {
            if ($repository->fork) {
                return false;
            }

            if (!$repository->permissions->push) {
                return false;
            }

            if (!$this->moduleService->isModule($repository)) {
                return false;
            }

            if ($this->moduleMapper->findByName($repository->name)) {
                return false;
            }

            return true;
        });
    }

    /**
     * @throws Exception\UnexpectedValueException
     * @throws Exception\RuntimeException
     * @return Http\Response
     */
    public function addAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $request = $this->getRequest();
        if (!$request->isPost()) {
            throw new Exception\UnexpectedValueException('Something went wrong with the post values of the request...');
        }

        $postParams = $request->getPost();

        $repo = $postParams->get('repo');
        $owner  = $postParams->get('owner');

        $repository = $this->repositoryRetriever->getUserRepositoryMetadata($owner, $repo);

        if (!($repository instanceof \stdClass)) {
            throw new Exception\RuntimeException(
                'Not able to fetch the repository from GitHub due to an unknown error.',
                Http\Response::STATUS_CODE_500
            );
        }

        if ($repository->fork || !$repository->permissions->push) {
            throw new Exception\UnexpectedValueException(
                'You have no permission to add this module. The reason might be that you are ' .
                'neither the owner nor a collaborator of this repository.',
                Http\Response::STATUS_CODE_403
            );
        }

        if (!$this->moduleService->isModule($repository)) {
            throw new Exception\UnexpectedValueException(
                $repository->name . ' is not a Zend Framework Module',
                Http\Response::STATUS_CODE_403
            );
        }

        $module = $this->moduleService->register($repository);
        $this->flashMessenger()->addMessage($module->getName() . ' has been added to ZF Modules');

        return $this->redirect()->toRoute('zfcuser');
    }

    /**
     * @throws Exception\UnexpectedValueException
     * @return Http\Response
     */
    public function removeAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }

        $request = $this->getRequest();
        if (!$request->isPost()) {
            throw new Exception\UnexpectedValueException('Something went wrong with the post values of the request...');
        }

        $postParams = $request->getPost();

        $repo = $postParams->get('repo');
        $owner  = $postParams->get('owner');

        $repository = $this->repositoryRetriever->getUserRepositoryMetadata($owner, $repo);

        if (!$repository instanceof \stdClass) {
            throw new Exception\RuntimeException(
                'Not able to fetch the repository from GitHub due to an unknown error.',
                Http\Response::STATUS_CODE_500
            );
        }

        if ($repository->fork || !$repository->permissions->push) {
            throw new Exception\UnexpectedValueException(
                'You have no permission to remove this module. The reason might be that you are ' .
                'neither the owner nor a collaborator of this repository.',
                Http\Response::STATUS_CODE_403
            );
        }

        $module = $this->moduleMapper->findByUrl($repository->html_url);

        if (!$module) {
            throw new Exception\UnexpectedValueException(
                $repository->name . ' was not found',
                Http\Response::STATUS_CODE_403
            );
        }

        $this->moduleMapper->delete($module);
        $this->flashMessenger()->addMessage($repository->name . ' has been removed from ZF Modules');

        return $this->redirect()->toRoute('zfcuser');
    }
}
