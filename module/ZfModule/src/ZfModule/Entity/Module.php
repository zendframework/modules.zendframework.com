<?php

namespace ZfModule\Entity;

class Module implements ModuleInterface
{
    /**
     * @var id
     */
    private $id = null;

    /**
     * @var string
     */
    private $name = null;

    /**
     * @var string
     */
    private $description = null;

    /**
     * @var string
     */
    private $url = null;

    /**
     * @var int
     */
    private $createdAt = null;

    /**
     * @var int
     */
    private $updatedAt = null;

    /**
     * @var string
     */
    private $owner = null;

    /**
     * @var string
     */
    private $photoUrl = null;

    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl($photoUrl)
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreateAtDateTime()
    {
        return new \DateTime($this->getCreatedAt());
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     * @return ModuleInterface
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Get Url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set Url
     *
     * @param string $url
     * @return ModuleInterface
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set Description
     *
     * @param string $description
     * @return ModuleInterface
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name
     * @return ModuleInterface
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
