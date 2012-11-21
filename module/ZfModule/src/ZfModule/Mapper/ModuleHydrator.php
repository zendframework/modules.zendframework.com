<?php

namespace ZfModule\Mapper;

use Zend\Stdlib\Hydrator\ClassMethods;
use ZfModule\Entity\ModuleInterface as ModuleEntityInterface;

class ModuleHydrator extends ClassMethods
{
    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function extract($object)
    {
        if (!$object instanceof ModuleEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of ZfModule\Entity\ModuleEntityInterface');
        }
        /* @var $object UserInterface*/
        $data = parent::extract($object);
        $data = $this->mapField('id', 'module_id', $data);
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return UserInterface
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof ModuleEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of ZfModule\Entity\ModuleEntityInterface');
        }
        $data = $this->mapField('module_id', 'id', $data);
        return parent::hydrate($data, $object);
    }

    protected function mapField($keyFrom, $keyTo, array $array)
    {
        $array[$keyTo] = $array[$keyFrom];
        unset($array[$keyFrom]);
        return $array;
    }
}
