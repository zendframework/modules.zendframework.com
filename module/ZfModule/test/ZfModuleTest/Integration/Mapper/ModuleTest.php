<?php

namespace ZfModuleTest\Integration\Mapper;

use ApplicationTest\Integration\Util\Bootstrap;
use PHPUnit_Framework_TestCase;
use Zend\Db;
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

        /* @var Db\Adapter\Adapter $database */
        $database = $serviceManager->get('zfcuser_zend_db_adapter');

        $this->connection = $database->getDriver()->getConnection();
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

        $this->assertSame(1, $paginator->getTotalItemCount());
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

        $results = $this->mapper->findByLike($query);

        $this->assertCount(1, $results);
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

    /**
     * @return Entity\Module
     */
    private function module()
    {
        static $id = 1;

        $module = new Entity\Module();

        $module->setId($id);
        $module->setName('');
        $module->setOwner('');
        $module->setDescription('');
        $module->setUrl('');

        $id++;

        return $module;
    }
}
