<?php

namespace Application\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Db\ResultSet\HydratingResultSet;

class Module extends AbstractDbMapper implements ModuleInterface
{
    protected $tableName  = 'module';

    public function pagination($page, $limit)
    {
        $sql = $this->getSql();
        $select = $this->getSelect()
            ->from($this->tableName);

        $adapter = new \Zend\Paginator\Adapter\DbSelect(
            $select,
            $this->getSql(),
            new HydratingResultSet($this->getHydrator(), $this->getEntityPrototype())
        );
        $paginator = new \Zend\Paginator\Paginator($adapter);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);

        return $paginator;
    }

    public function paginationSearch($page, $limit, $query)
    {
        $sql = $this->getSql();
        $select = $this->getSelect()
            ->from($this->tableName);

        $spec = function ( $where) use ($query) {
            $where->like('name', '%'.$query.'%')->or->like('description', '%'.$query.'%');
        };
        $select->where($spec);

        $adapter = new \Zend\Paginator\Adapter\DbSelect(
            $select,
            $this->getSql(),
            new HydratingResultSet($this->getHydrator(), $this->getEntityPrototype())
        );
        $paginator = new \Zend\Paginator\Paginator($adapter);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);

        return $paginator;
    }

    public function findAll($limit= null, $orderBy = null, $sort = 'ASC')
    {
        $select = $this->getSelect()
                       ->from($this->tableName);

        if($orderBy) {
            $select->order($orderBy . ' ' . $sort);
        }

        if($limit) {
            $select->limit($limit);
        }

        $entity = $this->select($select);
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function findByLike($query, $limit = null, $orderBy = null, $sort = 'ASC')
    {
        $select = $this->getSelect()
                       ->from($this->tableName);

        if($orderBy) {
            $select->order($orderBy . ' ' . $sort);
        }

        if($limit) {
            $select->limit($limit);
        }

        $spec = function ( $where) use ($query) {
            $where->like('name', '%'.$query.'%')->or->like('description', '%'.$query.'%');
        };
        $select->where($spec);

        $entity = $this->select($select);
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function findByName($name)
    {
        $select = $this->getSelect()
                       ->from($this->tableName)
                       ->where(array('name' => $name));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function findByUrl($url)
    {
        $select = $this->getSelect()
                       ->from($this->tableName)
                       ->where(array('url' => $url));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function findById($id)
    {
        $select = $this->getSelect()
                       ->from($this->tableName)
                       ->where(array('module_id' => $id));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function insert($entity, $tableName = null, HydratorInterface $hydrator = null)
    {
        $result = parent::insert($entity, $tableName, $hydrator);
        $entity->setId($result->getGeneratedValue());
        return $result;
    }

    public function update($entity, $where = null, $tableName = null, HydratorInterface $hydrator = null)
    {
        if (!$where) {
            $where = 'module_id = ' . $entity->getId();
        }

        return parent::update($entity, $where, $tableName, $hydrator);
    }

    public function delete($entity, $where = null, $tableName = null)
    {
        if (!$where) {
            $where = 'module_id = ' . $entity->getId();
        }
         return parent::delete($where, $tableName);
    }
}
