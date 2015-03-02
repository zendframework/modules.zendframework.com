<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfModule\Mapper;

class SearchController extends AbstractActionController
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

    public function indexAction()
    {
        $query =  $this->params()->fromQuery('query', null);

        $results = $this->moduleMapper->findByLike($query);

        $viewModel = new ViewModel([
            'results' => $results,
        ]);
        $viewModel->setTerminal(true);

        return $viewModel;
    }
}
