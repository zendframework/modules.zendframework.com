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

class RepolistController extends AbstractActionController
{
    public function indexAction()
    {
        $hybridAuth = $this->getServiceLocator()->get('HybridAuth');

        $adapter = $hybridAuth->getAdapter('github');
        $token = $adapter->getAccessToken();
        echo "<pre>";
        print_r($token);
        exit;
        
        return new ViewModel();
    }
}
