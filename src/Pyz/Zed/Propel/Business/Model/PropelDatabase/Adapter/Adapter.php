<?php

namespace Pyz\Zed\Propel\Business\Model\PropelDatabase\Adapter;

use Pyz\Zed\Propel\Business\Model\PropelDatabase\Command\DropDatabaseTablesInterface;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Adapter\Adapter as SprykerAdapter;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Command\CreateDatabaseInterface;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Command\DropDatabaseInterface;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Command\ExportDatabaseInterface;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Command\ImportDatabaseInterface;

class Adapter extends SprykerAdapter
{
    /**
     * @var DropDatabaseTablesInterface
     */
    protected $dropTablesCommand;

    /**
     * @param string $adapter
     * @param CreateDatabaseInterface $createCommand
     * @param DropDatabaseInterface $dropCommand
     * @param ExportDatabaseInterface $exportCommand
     * @param ImportDatabaseInterface $importCommand
     * @param DropDatabaseTablesInterface $dropTablesCommand
     */
    public function __construct(
        $adapter,
        CreateDatabaseInterface $createCommand,
        DropDatabaseInterface $dropCommand,
        ExportDatabaseInterface $exportCommand,
        ImportDatabaseInterface $importCommand,
        DropDatabaseTablesInterface $dropTablesCommand
    ) {
        parent::__construct($adapter, $createCommand, $dropCommand, $exportCommand, $importCommand);

        $this->dropTablesCommand = $dropTablesCommand;
    }

    /**
     * @return void
     */
    public function dropTables(): void
    {
        $this->dropTablesCommand->dropTables();
    }
}
