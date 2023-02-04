<?php
/**
 * Durst - project - PaymentMethodPostRemovePluginInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 12:04
 */

namespace Pyz\Zed\Merchant\Communication\Plugin;

use Orm\Zed\Merchant\Persistence\SpyPaymentMethod;

interface PaymentMethodPostRemovePluginInterface
{
    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyPaymentMethod $paymentMethod
     * @return void
     */
    public function remove(SpyPaymentMethod $paymentMethod): void;
}
