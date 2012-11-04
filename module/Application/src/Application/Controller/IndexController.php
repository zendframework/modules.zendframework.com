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

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $query =  $this->params()->fromQuery('q', false);

        $page = (int) $this->params()->fromRoute('page', 1);
        $sm = $this->getServiceLocator();
        $mapper = $this->getServiceLocator()->get('application_module_mapper');

        $search = null;
        if($query) {
            $search = "?q=" . $query;
            $repositories = $mapper->paginationSearch($page, 15, $query);
        } else {
           $repositories = $mapper->pagination($page, 15);
        }


        return array(
            'repositories' => $repositories,
            'search' => $search,
        );
    }
}
