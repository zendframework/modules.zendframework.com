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
        $this->layout()->foo = 'bar';
        return new ViewModel();
    }

    public function githubAction()
    {
        $sl = $this->getServiceLocator();
        $api = $sl->get('edpgithub_api_factory');

        $repoList = array();
        $service = $api->getService('Repo');
        $memberRepositories = $service->listRepositories(null, 'member');
       
        foreach($memberRepositories as $repo) {
            $repoList[$repo->getName()] = $repo;
        }

        $allRepositories = $service->listRepositories(null, 'all');
       
        foreach($allRepositories as $repo) {
            if(!$repo->getFork()) {
                $repoList[$repo->getName()] = $repo;
            }
        }
        
        return array('repositories' => $repoList, 'api' => $api);
    }
}
