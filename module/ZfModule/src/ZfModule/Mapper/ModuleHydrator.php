<?php

namespace ZfModule\Mapper;

use Zend\Stdlib\Hydrator\Reflection;
use ZfModule\Entity;

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
        if (!$object instanceof Entity\ModuleInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of ZfModule\Entity\ModuleEntityInterface');
        }

        $data = parent::extract($object);
        
        $this->changeKey('id', 'module_id', $data);
        $this->changeKey('createdAt', 'created_at', $data);
        $this->changeKey('updatedAt', 'updated_at', $data);
        $this->changeKey('photoUrl', 'photo_url', $data);

        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param Entity\ModuleInterface $object
     * @return Entity\ModuleInterface
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof Entity\ModuleInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of ZfModule\Entity\ModuleEntityInterface');
        }

        $this->changeKey('module_id', 'id', $data);
        $this->changeKey('created_at', 'createdAt', $data);
        $this->changeKey('updated_at', 'updatedAt', $data);
        $this->changeKey('photo_url', 'photoUrl', $data);

        return parent::hydrate($data, $object);
    }

    /**
     * @param string $source
     * @param string $target
     * @param array $data
     */
    private function changeKey($source, $target, array &$data)
    {
        $data[$target] = $data[$source];
        unset($data[$source]);
    }
}
