<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SearchController extends AbstractActionController
{
    public function indexAction()
    {
        $query =  $this->params()->fromRoute('query', null);
        $query = mysql_real_escape_string($query);

        $sm = $this->getServiceLocator();
        $mapper = $sm->get('application_module_mapper');

        $results = $mapper->findByLike($query);

        $viewModel = new ViewModel(array(
            'results' => $results,
        ));
        $viewModel->setTerminal(true);
        return $viewModel;
    }
}
