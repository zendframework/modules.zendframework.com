<?php

namespace User\Entity;

use ZfcUser\Entity\User as ZfcUser;

class User extends ZfcUser
{
    /**
     * @var string
     */
    private $photoUrl;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    /**
     * @return string
     */
    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    /**
     * @param string $photoUrl
     * @return self
     */
    public function setPhotoUrl($photoUrl)
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }
}
