<?php

namespace ZfModule\Mapper;

use Zend\Stdlib\Hydrator\Reflection;
use ZfModule\Entity\ModuleInterface as ModuleEntityInterface;

class ModuleHydrator extends Reflection
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

        $data = parent::extract($object);
        $data = $this->mapField('id', 'module_id', $data);
        $data = $this->mapField('createdAt', 'created_at', $data);
        $data = $this->mapField('updatedAt', 'updated_at', $data);
        $data = $this->mapField('photoUrl', 'photo_url', $data);

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
        $data = $this->mapField('created_at', 'createdAt', $data);
        $data = $this->mapField('updated_at', 'updatedAt', $data);
        $data = $this->mapField('photo_url', 'photoUrl', $data);

        return parent::hydrate($data, $object);
    }

    protected function mapField($keyFrom, $keyTo, array $array)
    {
        $array[$keyTo] = $array[$keyFrom];
        unset($array[$keyFrom]);
        return $array;
    }
}
