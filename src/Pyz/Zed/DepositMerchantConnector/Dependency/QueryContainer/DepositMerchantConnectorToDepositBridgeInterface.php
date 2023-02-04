<?php
/**
 * Durst - project - DepositMerchantConnectorToDepositBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-24
 * Time: 14:37
 */

namespace Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer;


use Orm\Zed\Deposit\Persistence\SpyDepositQuery;

interface DepositMerchantConnectorToDepositBridgeInterface
{
    /**
     * @return \Orm\Zed\Deposit\Persistence\SpyDepositQuery
     */
    public function queryDeposit(): SpyDepositQuery;
}