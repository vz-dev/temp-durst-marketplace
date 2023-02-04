<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 23.08.18
 * Time: 10:25
 */

namespace Pyz\Zed\Driver\Persistence;


use Orm\Zed\Driver\Persistence\DstDriverQuery;
use Orm\Zed\Driver\Persistence\Map\DstDriverTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * @method DriverPersistenceFactory getFactory()
 */
class DriverQueryContainer extends AbstractQueryContainer implements DriverQueryContainerInterface
{
    /**
     * @return DstDriverQuery
     */
    public function queryDriver() : DstDriverQuery
    {
        return $this
            ->getFactory()
            ->createDriverQuery();
    }

    /**
     * @param int $idDriver
     * @return DstDriverQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryDriverById(int $idDriver) : DstDriverQuery
    {
        return $this
            ->queryDriver()
            ->filterByIdDriver($idDriver);
    }

    /**
     * @param int $fkBranch
     * @param array $idDriversToExclude
     * @return \Orm\Zed\Driver\Persistence\DstDriverQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryActiveDriversByFkBranchAndExcludedDrivers(int $fkBranch, array $idDriversToExclude): DstDriverQuery
    {
        return $this
            ->queryDriver()
            ->filterByFkBranch($fkBranch)
            ->filterByIdDriver($idDriversToExclude, Criteria::NOT_IN)
            ->filterByStatus(DstDriverTableMap::COL_STATUS_ACTIVE);
    }
}
