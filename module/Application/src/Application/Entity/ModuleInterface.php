<?php

namespace Application\Entity;

interface ModuleInterface
{  
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
