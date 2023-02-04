<?php
/**
 * Durst - project - MerchantPersistenceFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 11:01
 */

namespace Pyz\Zed\Merchant\Persistence;

use Orm\Zed\Merchant\Persistence\DstBranchToDepositQuery;
use Orm\Zed\Merchant\Persistence\DstBranchUserQuery;
use Orm\Zed\Merchant\Persistence\DstMerchantUserQuery;
use Orm\Zed\Merchant\Persistence\SpyBranchQuery;
use Orm\Zed\Merchant\Persistence\SpyBranchToPaymentMethodQuery;
use Orm\Zed\Merchant\Persistence\SpyEnumSalutationQuery;
use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;
use Orm\Zed\Merchant\Persistence\SpyPaymentMethodQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class MerchantPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     */
    public function createMerchantQuery(): SpyMerchantQuery
    {
        return SpyMerchantQuery::create();
    }

    /**
     * @return \Orm\Zed\Merchant\Persistence\SpyBranchQuery
     */
    public function createBranchQuery(): SpyBranchQuery
    {
        return SpyBranchQuery::create();
    }

    /**
     * @return SpyPaymentMethodQuery
     */
    public function createPaymentMethodQuery(): SpyPaymentMethodQuery
    {
        return SpyPaymentMethodQuery::create();
    }

    /**
     * @return SpyBranchToPaymentMethodQuery
     */
    public function createBranchToPaymentMethodQuery(): SpyBranchToPaymentMethodQuery
    {
        return SpyBranchToPaymentMethodQuery::create();
    }

    /**
     * @return SpyEnumSalutationQuery
     */
    public function createEnumSalutationQuery(): SpyEnumSalutationQuery
    {
        return SpyEnumSalutationQuery::create();
    }

    /**
     * @return DstBranchToDepositQuery
     */
    public function createBranchToDepositQuery(): DstBranchToDepositQuery
    {
        return DstBranchToDepositQuery::create();
    }

    /**
     * @return \Orm\Zed\Merchant\Persistence\DstBranchUserQuery
     */
    public function createBranchUserQuery(): DstBranchUserQuery
    {
        return DstBranchUserQuery::create();
    }

    /**
     * @return \Orm\Zed\Merchant\Persistence\DstMerchantUserQuery
     */
    public function createMerchantUserQuery(): DstMerchantUserQuery
    {
        return DstMerchantUserQuery::create();
    }
}
