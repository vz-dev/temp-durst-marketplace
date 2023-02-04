<?php
/**
 * Durst - project - BillingToMerchantQueryContainerBridgeInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-27
 * Time: 15:35
 */

namespace Pyz\Zed\Billing\Dependency\Persistence;


use Orm\Zed\Merchant\Persistence\SpyBranchQuery;
use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;

interface BillingToMerchantQueryContainerBridgeInterface
{
    /**
     * @return SpyMerchantQuery
     */
    public function queryMerchant() : SpyMerchantQuery;

    /**
     * @return SpyBranchQuery
     */
    public function queryBranch() : SpyBranchQuery;
}
