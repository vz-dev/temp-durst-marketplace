<?php
/**
 * Durst - project - PaymentHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 14:58
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;


use Generated\Shared\Transfer\PaymentTransfer;
use Pyz\Shared\Integra\IntegraConstants;

class PaymentHydrator implements PaymentHydratorInterface
{
    /**
     * @return \Generated\Shared\Transfer\PaymentTransfer
     */
    public function createPaymentTransfer(): PaymentTransfer
    {
        return (new PaymentTransfer())
            ->setPaymentSelection(IntegraConstants::INTEGRA_NO_PAYMENT);
    }
}
