<?php

namespace Pyz\Zed\Propel\Business;

use Spryker\Zed\Propel\Business\PropelFacadeInterface as SprykerPropelFacadeInterface;

interface PropelFacadeInterface extends SprykerPropelFacadeInterface
{
    /**
     * Specification:
     * - Runs raw SQL script for dropping all database tables, without dropping the database.
     *
     * @api
     *
     * @return void
     */
    public function dropDatabaseTables(): void;
}
