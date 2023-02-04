<?php
/**
 * Durst - project - PaymentMethodPostRemoveTouchPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 19.11.18
 * Time: 21:26
 */

namespace Pyz\Zed\Touch\Communication\Plugin\Merchant;

use Orm\Zed\Merchant\Persistence\SpyPaymentMethod;
use Pyz\Shared\Merchant\MerchantConstants;
use Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodPostRemovePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class PaymentMethodPostRemoveTouchPlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\Merchant
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 */
class PaymentMethodPostRemoveTouchPlugin extends AbstractPlugin implements PaymentMethodPostRemovePluginInterface
{
    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyPaymentMethod $paymentMethod
     *
     * @return void
     */
    public function remove(SpyPaymentMethod $paymentMethod): void
    {
        $this->getFacade()->touchDeleted(MerchantConstants::RESOURCE_TYPE_PAYMENT_PROVIDER, $paymentMethod->getIdPaymentMethod());
    }
}
