<?php

namespace ZfModuleTest\Integration\Mapper;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;
use Zend\Db;
use Zend\Db\Adapter\Exception\RuntimeException as DbRuntimeException;
use ZfModule\Entity;
use ZfModule\Mapper;

/**
 * @coversNothing
 */
class ModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mapper\Module
     */
    private $mapper;

    /**
     * @var Db\Adapter\Driver\ConnectionInterface
     */
    private $connection;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->mapper = $serviceManager->get(Mapper\Module::class);

        /* @var  $database Db\Adapter\Adapter */
        $database = $serviceManager->get('zfcuser_zend_db_adapter');

        $this->connection = $database->getDriver()->getConnection();

        try {
            $this->connection->connect();
        } catch (DbRuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }

        $this->connection->beginTransaction();
    }

    protected function tearDown()
    {
        $this->connection->rollback();

        unset($this->mapper);
        unset($this->connection);
    }

    /**
     * @dataProvider providerProperties
     *
     * @param $property
     */
    public function testPaginationFindsInProperty($property)
    {
        $value = 'foo bar baz';
        $query = 'bar';

        $module = $this->module();

        $setter = 'set' . ucfirst($property);

        $module->{$setter}($value);

        $this->mapper->insert($module);

        $paginator = $this->mapper->pagination(1, 100, $query);

        /* @var Db\ResultSet\HydratingResultSet $resultSet */
        $resultSet = $paginator->getCurrentItems();

        $this->assertCount(1, $resultSet);
    }

    /**
     * @dataProvider providerProperties
     *
     * @param $property
     */
    public function testFindByLikeFindsInProperty($property)
    {
        $value = 'foo bar baz';
        $query = 'bar';

        $module = $this->module();

        $setter = 'set' . ucfirst($property);

        $module->{$setter}($value);

        $this->mapper->insert($module);

        /* @var Db\ResultSet\HydratingResultSet $resultSet */
        $resultSet = $this->mapper->findByLike($query);

        $this->assertCount(1, $resultSet);
    }

    public function testFindByReturnsNullIfNothingWasFound()
    {
        $this->assertNull($this->mapper->findBy('name', 'foo'));
    }

    /**
     * @return array
     */
    public function providerProperties()
    {
        return [
            [
                'name',
            ],
            [
                'description',
            ],
            [
                'owner',
            ],
        ];
    }

    public function testPaginationMatchesEntitiesWhereAllWordsExist()
    {
        $value = 'foo bar baz qux';
        $query = 'foo baz';

        $module = $this->module();
        $module->setDescription($value);
        $this->mapper->insert($module);

        $moduleFoo = $this->module();
        $moduleFoo->setDescription('foo');
        $this->mapper->insert($moduleFoo);

        $moduleBaz = $this->module();
        $moduleBaz->setDescription('baz');
        $this->mapper->insert($moduleBaz);

        $paginator = $this->mapper->pagination(1, 100, $query);

        /* @var Db\ResultSet\HydratingResultSet $resultSet */
        $resultSet = $paginator->getCurrentItems();

        $this->assertCount(1, $resultSet);

        /* @var Entity\Module $result */
        $result = $resultSet->current();

        $this->assertInstanceOf(Entity\Module::class, $result);
        $this->assertSame($result->getDescription(), $module->getDescription());
    }

    public function testFindByLikeMatchesEntitiesWhereAllWordsExist()
    {
        $value = 'foo bar baz qux';
        $query = 'foo baz';

        $module = $this->module();
        $module->setDescription($value);
        $this->mapper->insert($module);

        $moduleFoo = $this->module();
        $moduleFoo->setDescription('foo');
        $this->mapper->insert($moduleFoo);

        $moduleBaz = $this->module();
        $moduleBaz->setDescription('baz');
        $this->mapper->insert($moduleBaz);

        /* @var Db\ResultSet\HydratingResultSet $resultSet */
        $resultSet = $this->mapper->findByLike($query);

        $this->assertCount(1, $resultSet);

        /* @var Entity\Module $result */
        $result = $resultSet->current();

        $this->assertInstanceOf(Entity\Module::class, $result);

        $this->assertSame($result->getDescription(), $module->getDescription());
    }

    /**
     * @return Entity\Module
     */
    private function module()
    {
        $module = new Entity\Module();

        $module->setName('');
        $module->setOwner('');
        $module->setDescription('');
        $module->setUrl('');

        return $module;
    }
}
