<?php
/**
 * Durst - project - PaymentMethodToBranchPostRemovePluginInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 12:05
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Orm\Zed\Merchant\Persistence\SpyBranchToPaymentMethod;

interface PaymentMethodToBranchPostRemovePluginInterface
{
    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyBranchToPaymentMethod $branchToPaymentMethod
     * @return void
     */
    public function remove(SpyBranchToPaymentMethod $branchToPaymentMethod): void;
}
