<?php
/**
 * Durst - project - PaymentMethodPostSaveTouchPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 19.11.18
 * Time: 21:27
 */

namespace Pyz\Zed\Touch\Communication\Plugin\Merchant;

use Orm\Zed\Merchant\Persistence\SpyPaymentMethod;
use Pyz\Shared\Merchant\MerchantConstants;
use Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodPostSavePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class PaymentMethodPostSaveTouchPlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\Merchant
 * @method \Spryker\Zed\Touch\Business\TouchFacade getFacade()
 */
class PaymentMethodPostSaveTouchPlugin extends AbstractPlugin implements PaymentMethodPostSavePluginInterface
{
    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyPaymentMethod $paymentMethod
     *
     * @return void
     */
    public function save(SpyPaymentMethod $paymentMethod): void
    {
        $this->getFacade()->touchActive(MerchantConstants::RESOURCE_TYPE_PAYMENT_PROVIDER, $paymentMethod->getIdPaymentMethod());
    }
}
