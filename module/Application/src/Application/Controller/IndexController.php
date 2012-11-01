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
        $sm = $this->getServiceLocator();
        $repos = $sm->get('EdpGithub\Client')->api('current_user')->repos();

        $mapper = $this->getServiceLocator()->get('application_module_mapper');
        $repositories = $mapper->findAll();
        return array('repositories' => $repositories);
    }
}
