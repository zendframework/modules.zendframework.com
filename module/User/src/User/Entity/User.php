<?php

namespace User\Entity;

use ZfcUser\Entity\User as ZfcUser;

class User extends ZfcUser
{
    /**
     * @var string
     */
    protected $photoUrl;

    /**
     * get Photo Url
     * @return string
     */
    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    /**
     * set Photo Url
     * @param string $photo
     * @return UserInterface
     */
    public function setPhotoUrl($photoUrl)
    {
        $this->photoUrl = $photoUrl;
        return $this;
    }
}