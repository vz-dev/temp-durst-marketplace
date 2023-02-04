<?php

namespace Pyz\Zed\DriverApp\Persistence;

use Orm\Zed\DriverApp\Persistence\DstDriverAppReleaseQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * @method \Pyz\Zed\DriverApp\Persistence\DriverAppPersistenceFactory getFactory()
 */
class DriverAppQueryContainer extends AbstractQueryContainer implements DriverAppQueryContainerInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\DriverApp\Persistence\DstDriverAppReleaseQuery
     */
    public function queryDriverAppRelease(): DstDriverAppReleaseQuery
    {
        return $this
            ->getFactory()
            ->createDriverAppReleaseQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\DriverApp\Persistence\DstDriverAppReleaseQuery
     */
    public function queryLatestRelease(): DstDriverAppReleaseQuery
    {
        return $this
            ->queryDriverAppRelease()
            ->orderByCreatedAt(Criteria::DESC);
    }
}
