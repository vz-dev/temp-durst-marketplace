<?php
/**
 * Durst - project - PaymentMethodNameHydratePlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 12:06
 */

namespace Pyz\Zed\Merchant\Communication\Plugin\Sales;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * Class PaymentMethodNameHydratePlugin
 * @package Pyz\Zed\Merchant\Communication\Plugin\Sales
 *
 * @method MerchantFacadeInterface getFacade()
 */
class PaymentMethodNameHydratePlugin extends AbstractPlugin implements HydrateOrderPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrate(OrderTransfer $orderTransfer): OrderTransfer
    {
        if ($orderTransfer->getPayments() === null ||
            $orderTransfer->getPayments()->offsetExists(0) !== true
        ) {
            return $orderTransfer;
        }

        $paymentMethodTransfer = $this
            ->getFacade()
            ->getPaymentMethodByCode(
                $this
                    ->getPaymentCode(
                        $orderTransfer
                            ->getPayments()
                            ->offsetGet(0)
                    )
            );

        return $orderTransfer
            ->setPaymentMethodName(
                $paymentMethodTransfer
                    ->getName()
            );
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentTransfer $paymentTransfer
     * @return string
     */
    protected function getPaymentCode(PaymentTransfer $paymentTransfer): string
    {
        return $paymentTransfer
            ->getPaymentMethod();
    }
}
