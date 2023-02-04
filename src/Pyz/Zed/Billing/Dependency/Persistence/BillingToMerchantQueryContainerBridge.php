<?php
/**
 * Durst - project - BillingToMerchantQueryContainerBridge.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-27
 * Time: 15:35
 */

namespace Pyz\Zed\Billing\Dependency\Persistence;

use Orm\Zed\Merchant\Persistence\SpyBranchQuery;
use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;

class BillingToMerchantQueryContainerBridge implements BillingToMerchantQueryContainerBridgeInterface
{
    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $merchantQueryContainer;

    /**
     * BillingToMerchantQueryContainerBridge constructor.
     * @param MerchantQueryContainerInterface $merchantQueryContainer
     */
    public function __construct(MerchantQueryContainerInterface $merchantQueryContainer)
    {
        $this->merchantQueryContainer = $merchantQueryContainer;
    }

    /**
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     */
    public function queryMerchant() : SpyMerchantQuery
    {
        return $this
            ->merchantQueryContainer
            ->queryMerchant();
    }

    /**
     * @return \Orm\Zed\Merchant\Persistence\SpyBranchQuery
     */
    public function queryBranch() : SpyBranchQuery
    {
        return $this
            ->merchantQueryContainer
            ->queryBranch();
    }
}
