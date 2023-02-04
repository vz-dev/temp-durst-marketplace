<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 23.08.18
 * Time: 10:26
 */

namespace Pyz\Zed\Driver\Persistence;


use Orm\Zed\Driver\Persistence\DstDriverQuery;

interface DriverQueryContainerInterface
{
    /**
     * @return DstDriverQuery
     */
    public function queryDriver() : DstDriverQuery;

    /**
     * @param int $idDriver
     * @return DstDriverQuery
     */
    public function queryDriverById(int $idDriver) : DstDriverQuery;

    /**
     * @param int $fkBranch
     * @param int[] $idDriversToExclude
     * @return \Orm\Zed\Driver\Persistence\DstDriverQuery
     */
    public function queryActiveDriversByFkBranchAndExcludedDrivers(int $fkBranch, array $idDriversToExclude): DstDriverQuery;
}
