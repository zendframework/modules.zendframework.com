<?php
namespace ZfModule\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfModule\Mapper;

class UserController extends AbstractActionController
{
    /**
     * @var Mapper\Module
     */
    private $moduleMapper;

    /**
     * @param Mapper\Module $moduleMapper
     */
    public function __construct(
        Mapper\Module $moduleMapper
    ) {
        $this->moduleMapper = $moduleMapper;
    }

    public function modulesForUserAction()
    {
        $query =  $this->params()->fromQuery('query', null);
        $page = (int) $this->params()->fromQuery('page', 1);
        $owner = $this->params()->fromRoute('owner');

        $modules = $this->moduleMapper->pagination($page, 10, $owner, 'created_at', "DESC");

        $viewModel = new ViewModel([
            'modules' => $modules,
            'query' => $query,
         ]);

        return $viewModel;
    }
}
