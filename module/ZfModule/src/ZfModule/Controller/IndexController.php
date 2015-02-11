<?php

namespace ZfModule\Controller;

use Application\Service\RepositoryRetriever;
use EdpGithub\Collection\RepositoryCollection;
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

        $params = [
            'type'      => 'all',
            'per_page'  => 100,
            'sort'      => 'updated',
            'direction' => 'desc',
        ];

        $repos = $this->repositoryRetriever->getAuthenticatedUserRepositories($params);
        $repositories = $this->fetchModules($repos);

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
        $params = [
            'per_page'  => 100,
            'sort'      => 'updated',
            'direction' => 'desc',
        ];

        $repos = $this->repositoryRetriever->getUserRepositories($owner, $params);
        $repositories = $this->fetchModules($repos);

        $viewModel = new ViewModel(['repositories' => $repositories]);
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('zf-module/index/index.phtml');

        return $viewModel;
    }

    /**
     * @param RepositoryCollection $repositories
     * @return array
     */
    private function fetchModules(RepositoryCollection $repositories)
    {
        $modules = [];

        foreach ($repositories as $repository) {
            if ($repository->fork) {
                continue;
            }

            if (!$repository->permissions->push) {
                continue;
            }

            if (!$this->moduleService->isModule($repository)) {
                continue;
            }

            if ($this->moduleMapper->findByName($repository->name)) {
                continue;
            }

            $modules[] = $repository;
        }

        return $modules;
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

            $repository = $this->repositoryRetriever->getUserRepositoryMetadata($owner, $repo);

            if (!($repository instanceof \stdClass)) {
                throw new Exception\RuntimeException(
                    'Not able to fetch the repository from github due to an unknown error.',
                    Http\Response::STATUS_CODE_500
                );
            }

            if (!$repository->fork && $repository->permissions->push) {
                if ($this->moduleService->isModule($repository)) {
                    $module = $this->moduleService->register($repository);
                    $this->flashMessenger()->addMessage($module->getName() . ' has been added to ZF Modules');
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
        if ($request->isPost()) {
            $repo = $request->getPost()->get('repo');
            $owner  = $request->getPost()->get('owner');

            $repository = $this->repositoryRetriever->getUserRepositoryMetadata($owner, $repo);

            if (!$repository instanceof \stdClass) {
                throw new Exception\RuntimeException(
                    'Not able to fetch the repository from github due to an unknown error.',
                    Http\Response::STATUS_CODE_500
                );
            }

            if (!$repository->fork && $repository->permissions->push) {
                $module = $this->moduleMapper->findByUrl($repository->html_url);
                if ($module instanceof \ZfModule\Entity\Module) {
                    $this->moduleMapper->delete($module);
                    $this->flashMessenger()->addMessage($repository->name . ' has been removed from ZF Modules');
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

        return $this->redirect()->toRoute('zfcuser');
    }
}
