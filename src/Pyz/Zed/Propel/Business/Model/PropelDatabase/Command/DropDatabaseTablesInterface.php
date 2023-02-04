<?php

namespace Pyz\Zed\Propel\Business\Model\PropelDatabase\Command;

interface DropDatabaseTablesInterface
{
    /**
     * @return void
     */
    public function dropTables(): void;
}
