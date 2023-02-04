<?php

namespace Pyz\Zed\Propel\Business;

use Pyz\Zed\Propel\Business\Model\PropelDatabase\Adapter\AdapterFactory;
use Spryker\Zed\Propel\Business\PropelBusinessFactory as SprykerPropelBusinessFactory;

use Spryker\Zed\Propel\PropelConfig;

/**
 * @method PropelConfig getConfig()
 */
class PropelBusinessFactory extends SprykerPropelBusinessFactory
{
    /**
     * @return AdapterFactory
     */
    protected function createAdapterFactory(): AdapterFactory
    {
        return new AdapterFactory();
    }
}
