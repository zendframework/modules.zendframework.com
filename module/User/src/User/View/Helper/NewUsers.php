<?php

namespace User\View\Helper;

use User\Mapper;
use Zend\View\Helper\AbstractHelper;

class NewUsers extends AbstractHelper
{
    /**
     * @var Mapper\User
     */
    private $userMapper;

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

        return $this->getView()->render('user/helper/new-users', [
            'users' => $users,
        ]);
    }
}
