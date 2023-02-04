<?php

namespace Pyz\Zed\DriverApp\Persistence;

use Orm\Zed\DriverApp\Persistence\DstDriverAppReleaseQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \Pyz\Zed\DriverApp\DriverAppConfig getConfig()
 * @method \Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainer getQueryContainer()
 */
class DriverAppPersistenceFactory extends AbstractPersistenceFactory
{
    public function createDriverAppReleaseQuery(): DstDriverAppReleaseQuery
    {
        return DstDriverAppReleaseQuery::create();
    }
}
