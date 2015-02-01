<?php

namespace User\View\Helper;

use User\Mapper;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ViewModel;

class NewUsers extends AbstractHelper
{
    /**
     * @var Mapper\User
     */
    private $userMapper;

    /**
     * $var string template used for view
     */
    protected $viewTemplate;

    /**
     * @param Mapper\User $userMapper
     */
    public function __construct(Mapper\User $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    /**
     * __invoke
     *
     * @access public
     * @return string
     */
    public function __invoke()
    {
        $users = $this->userMapper->findAll(16, 'created_at', 'DESC');

        $vm = new ViewModel([
            'users' => $users,
        ]);
        $vm->setTemplate('user/helper/new-users');

        return $this->getView()->render($vm);
    }

    /**
     * @param string $viewTemplate
     * @return NewUsers
     */
    public function setViewTemplate($viewTemplate)
    {
        $this->viewTemplate = $viewTemplate;

        return $this;
    }
}
