<?php

namespace User\View\Helper;

use EdpGithub\Client;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class UserOrganizations extends AbstractHelper
{
    /**
     * @var Client
     */
    private $githubClient;

    /**
     * $var string template used for view
     */
    protected $viewTemplate;

    /**
     * @param Client $githubClient
     */
    public function __construct(Client $githubClient)
    {
        $this->githubClient = $githubClient;
    }

    /**
     * __invoke
     *
     * @access public
     * @return string
     */
    public function __invoke()
    {
        $orgs = $this->githubClient->api('current_user')->orgs();
        $vm = new ViewModel([
            'orgs' => $orgs
        ]);
        $vm->setTemplate('user/helper/user-organizations.phtml');

        return $this->getView()->render($vm);
    }
}
