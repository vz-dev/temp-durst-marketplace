<?php

namespace Pyz\Zed\Propel\Business\Model\PropelDatabase\Adapter;

use Pyz\Zed\Propel\Business\Model\PropelDatabase\Adapter\PostgreSql\DropPostgreSqlDatabaseTables;
use Pyz\Zed\Propel\Business\Model\PropelDatabase\Command\DropDatabaseTablesInterface;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Adapter\AdapterFactory as SprykerAdapterFactory;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Adapter\AdapterInterface;
use Spryker\Zed\Propel\PropelConfig;

class AdapterFactory extends SprykerAdapterFactory
{
    /**
     * @return AdapterInterface
     */
    public function createPostgreSqlAdapter(): AdapterInterface
    {
        $postgreSqlAdapter = new Adapter(
            PropelConfig::DB_ENGINE_PGSQL,
            $this->createPostgreSqlCreateCommand(),
            $this->createPostgreSqlDropCommand(),
            $this->createPostgreSqlExportCommand(),
            $this->createPostgreSqlImportCommand(),
            $this->createDropPostgreSqlDatabaseTablesCommand()
        );

        return $postgreSqlAdapter;
    }

    /**
     * @return DropDatabaseTablesInterface
     */
    public function createDropPostgreSqlDatabaseTablesCommand(): DropDatabaseTablesInterface
    {
        return new DropPostgreSqlDatabaseTables();
    }
}
