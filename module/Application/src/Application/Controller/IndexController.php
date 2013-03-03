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
        $query =  $this->params()->fromQuery('query', null);

        $page = (int) $this->params()->fromRoute('page', 1);
        $mapper = $this->getServiceLocator()->get('zfmodule_mapper_module');

        $repositories = $mapper->pagination($page, 15, $query, 'created_at', 'DESC');

        return array(
            'repositories' => $repositories,
            'query' => $query,
        );
    }
}
