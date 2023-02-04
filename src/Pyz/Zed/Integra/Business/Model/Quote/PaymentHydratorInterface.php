<?php
/**
 * Durst - project - PaymentHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 14:58
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;


use Generated\Shared\Transfer\PaymentTransfer;

interface PaymentHydratorInterface
{
    /**
     * @return \Generated\Shared\Transfer\PaymentTransfer
     */
    public function createPaymentTransfer(): PaymentTransfer;
}
