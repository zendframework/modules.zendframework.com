<?php

namespace User\Controller;

use Zend\View\Model\ViewModel;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;
use ZfcUser\Controller\UserController as ZfcUserController;

/**
 * @method array listModule(array $options)
 * @method ZfcUserAuthentication zfcUserAuthentication()
 */
class UserController extends ZfcUserController
{
    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $viewModel = new ViewModel([
            'modules' => $this->listModule([
                'user' => true,
            ]),
        ]);

        $viewModel->setTemplate('zfc-user/user/index');

        return $viewModel;
    }
}
