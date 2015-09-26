<?php

namespace ApplicationTest\Integration\DBAL\Migration;

use ApplicationTest\Integration\Util\Bootstrap;
use Doctrine\DBAL;

class MigrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DBAL\Migrations\Configuration\Configuration
     */
    private $configuration;

    protected function tearDown()
    {
        $this->migrateUpTo();
    }

    /**
     * @dataProvider providerMigration
     *
     * @param string $identifier
     * @param string $previousIdentifier
     */
    public function testMigration($identifier, $previousIdentifier)
    {
        $this->migrateUpTo($previousIdentifier);

        $this->assertCanMigrateUpTo($identifier);
        $this->assertCanMigrateDownFrom($identifier, $previousIdentifier);
    }

    /**
     * @return \Generator
     */
    public function providerMigration()
    {
        $previousIdentifier = '0';

        foreach ($this->getMigrationConfiguration()->getAvailableVersions() as $identifier) {
            yield [
                $identifier,
                $previousIdentifier,
            ];

            $previousIdentifier = $identifier;
        };
    }

    /**
     * Asserts that the migration with the specified version can be rolled back.
     *
     * @param string $identifier
     * @param string $previousIdentifier
     *
     * @throws \Exception
     */
    private function assertCanMigrateDownFrom($identifier, $previousIdentifier)
    {
        $version = $this->getMigrationConfiguration()->getVersion($identifier);

        try {
            $version->execute('down');
        } catch (\Exception $exception) {
            $this->fail(sprintf(
                'Failed asserting that the version "%s" can be rolled back from - %s',
                $identifier,
                $exception->getMessage()
            ));

            throw $exception;
        }

        $this->assertSame($previousIdentifier, $this->getMigrationConfiguration()->getCurrentVersion());
    }

    /**
     * Asserts that the migration with the specified version can be rolled to.
     *
     * @param string $identifier
     *
     * @throws DBAL\Migrations\MigrationException
     * @throws \Exception
     */
    private function assertCanMigrateUpTo($identifier)
    {
        $version = $this->getMigrationConfiguration()->getVersion($identifier);

        try {
            $version->execute('up');
        } catch (\Exception $exception) {
            $this->fail(sprintf(
                'Failed asserting that the version "%s" can be rolled up to - %s',
                $identifier,
                $exception->getMessage()
            ));

            throw $exception;
        }

        $this->assertSame($identifier, $this->getMigrationConfiguration()->getCurrentVersion());
    }

    /**
     * Ensure that the migration with the specified version has been migrated up to.
     *
     * @param string $identifier
     */
    private function migrateUpTo($identifier = null)
    {
        $migration = new DBAL\Migrations\Migration($this->getMigrationConfiguration());
        $migration->migrate($identifier);
    }

    /**
     * @return DBAL\Migrations\Configuration\Configuration
     */
    private function getMigrationConfiguration()
    {
        if ($this->configuration === null) {
            $serviceManager = Bootstrap::getServiceManager();

            /* @var DBAL\Migrations\Configuration\Configuration $migrationConfiguration */
            $this->configuration = $serviceManager->get('doctrine.migrations_configuration.orm_default');
        }

        return $this->configuration;
    }
}
