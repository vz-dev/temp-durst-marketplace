<?php
/**
 * Durst - project - PayPalInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.01.19
 * Time: 13:54
 */

namespace Pyz\Zed\HeidelpayRest\Business\PaymentType;

use heidelpayPHP\Resources\Customer;
use heidelpayPHP\Resources\Metadata;
use heidelpayPHP\Resources\PaymentTypes\Paypal;
use heidelpayPHP\Resources\TransactionTypes\Authorization;

interface PayPalTypeInterface
{
    /**
     * @param \heidelpayPHP\Resources\PaymentTypes\Paypal $paymentType
     * @param float $amount
     * @param string|null $returnUrl
     * @param \heidelpayPHP\Resources\Customer|null $customer
     * @param \heidelpayPHP\Resources\Metadata|null $metadata
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Authorization
     */
    public function authorize(
        Paypal $paymentType,
        float $amount,
        ?string $returnUrl = null,
        ?Customer $customer = null,
        ?Metadata $metadata = null
    ): Authorization;
}
