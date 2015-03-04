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
    public function __construct(Mapper\Module $moduleMapper)
    {
        $this->moduleMapper = $moduleMapper;
    }

    public function modulesForUserAction()
    {
        $params = $this->params();
        $query =  $params->fromQuery('query', null);
        $page = (int) $params->fromQuery('page', 1);
        $owner = $params->fromRoute('owner');

        $modules = $this->moduleMapper->pagination($page, 10, $owner, 'created_at', 'DESC');

        return new ViewModel([
            'modules' => $modules,
            'query' => $query,
        ]);
    }
}
