<?php
/**
 * Durst - project - DepositMerchantConnectorToMerchantPriceBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-24
 * Time: 14:37
 */

namespace Pyz\Zed\DepositMerchantConnector\Dependency\QueryContainer;


use Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery;

interface DepositMerchantConnectorToMerchantPriceBridgeInterface
{
    /**
     * @param int $idBranch
     *
     * @return \Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery
     */
    public function queryPricesByIdBranch(int $idBranch): MerchantPriceQuery;
}