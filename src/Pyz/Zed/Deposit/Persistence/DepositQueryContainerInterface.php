<?php

namespace Pyz\Zed\Deposit\Persistence;

use Orm\Zed\Deposit\Persistence\SpyDepositQuery;

interface DepositQueryContainerInterface
{
    /**
     * Returns a deposit query filtered by its primary key.
     * This will return one result at max as this column is unique.
     *
     * @param int $idDeposit
     * @return \Orm\Zed\Deposit\Persistence\SpyDepositQuery
     */
    public function queryDepositByIdDeposit($idDeposit);

    /**
     * Returns a deposit query filtered by its name.
     * This will return one result at max as this column is unique.
     *
     * @param string $name
     * @return SpyDepositQuery
     */
    public function queryDepositByName($name);

    /**
     * Returns an unfiltered deposit query.
     *
     * @return SpyDepositQuery
     */
    public function queryDeposit();
}
