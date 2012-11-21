<?php

namespace ZfModule\Entity;

interface ModuleInterface
{
    /**
     * Get Owner
     * @return string
     */
    public function getOwner();

    /**
     * Set Owner
     */
    public function setOwner($owner);

    /**
     * Get Photo URL
     *
     * @return string
     */
    public function getPhotoUrl();
    /**
     * Set Photo URL
     */
    public function setPhotoUrl($photoUrl);

    /**
     * Get Updated at
     *
     * @return int
     */
    public function getUpdatedAt();

    /**
     * Set Updated at
     * @param int $updatedAt
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get Created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set Created at
     *
     * @param int $createdAt
     */
    public function setCreatedAt($createdAt);

    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set id.
     *
     * @param int $id
     * @return ModuleInterface
     */
    public function setId($id);

    /**
     * Get Url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Set Url
     *
     * @param string $url
     * @return ModuleInterface
     */
    public function setUrl($url);

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set Description
     *
     * @param string $description
     * @return ModuleInterface
     */
    public function setDescription($description);

    /**
     * Get Name
     *
     * @return string
     */
    public function getName();

    /**
     * Set Name
     *
     * @param string $name
     * @return ModuleInterface
     */
    public function setName($name);
}
