<?php

namespace Pyz\Zed\DriverApp\Persistence;

use Orm\Zed\DriverApp\Persistence\DstDriverAppReleaseQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface DriverAppQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @return \Orm\Zed\DriverApp\Persistence\DstDriverAppReleaseQuery
     */
    public function queryDriverAppRelease(): DstDriverAppReleaseQuery;

    /**
     * @return \Orm\Zed\DriverApp\Persistence\DstDriverAppReleaseQuery
     */
    public function queryLatestRelease(): DstDriverAppReleaseQuery;
}
