<?php

namespace User\Controller;

use Zend\View\Model\ViewModel;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;
use ZfcUser\Controller\UserController as ZfcUserController;
use ZfModule\Service;

/**
 * @method array listModule(array $options)
 * @method ZfcUserAuthentication zfcUserAuthentication()
 */
class UserController extends ZfcUserController
{
    /**
     * @var Service\Module
     */
    private $moduleService;

    /**
     * @param Service\Module $moduleService
     */
    public function __construct(Service\Module $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $viewModel = new ViewModel([
            'modules' => $this->moduleService->currentUserModules(),
        ]);

        $viewModel->setTemplate('zfc-user/user/index');

        return $viewModel;
    }
}
