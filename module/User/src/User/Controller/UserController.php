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
     * @param callable $redirectCallback
     * @param Service\Module $moduleService
     */
    public function __construct($redirectCallback, Service\Module $moduleService)
    {
        if (!is_callable($redirectCallback)) {
            throw new \InvalidArgumentException('You must supply a callable redirectCallback');
        }
        $this->redirectCallback = $redirectCallback;
        $this->moduleService = $moduleService;
    }

    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $viewModel = new ViewModel([
            'repositories' => $this->moduleService->currentUserModules(),
        ]);

        $viewModel->setTemplate('zfc-user/user/index');

        return $viewModel;
    }
}
