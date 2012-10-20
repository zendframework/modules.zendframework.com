<?php

namespace Application\Entity;

class Module implements ModuleInterface
{
    /**
     * @var id
     */
    protected $id = null;
    
    /**
     * @var string
     */
    protected $name = null;
    
    /**
     * @var string
     */
    protected $description = null;
    
    /**
     * @var string
     */
    protected $url = null;
    
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