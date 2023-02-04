<?php

namespace Pyz\Zed\Propel\Business;

use Pyz\Zed\Propel\Business\PropelBusinessFactory;
use Spryker\Zed\Propel\Business\PropelFacade as SprykerPropelFacade;

/**
 * @method PropelBusinessFactory getFactory()
 */
class PropelFacade extends SprykerPropelFacade implements PropelFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return void
     */
    public function dropDatabaseTables(): void
    {
        $this->getFactory()
            ->createPropelDatabaseAdapterCollection()
            ->getAdapter()
            ->dropTables();
    }
}
