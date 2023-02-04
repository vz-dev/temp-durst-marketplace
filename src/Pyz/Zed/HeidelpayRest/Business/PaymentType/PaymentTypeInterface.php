<?php
/**
 * Durst - project - PaymentTypeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.01.19
 * Time: 14:22
 */

namespace Pyz\Zed\HeidelpayRest\Business\PaymentType;

use heidelpayPHP\Resources\Customer;
use heidelpayPHP\Resources\Metadata;
use heidelpayPHP\Resources\PaymentTypes\BasePaymentType;
use heidelpayPHP\Resources\TransactionTypes\Authorization;

interface PaymentTypeInterface
{
    /**
     * @param \heidelpayPHP\Resources\PaymentTypes\BasePaymentType $paymentType
     * @param float $amount
     * @param null|string $returnUrl
     * @param \heidelpayPHP\Resources\Customer|null $customer
     * @param \heidelpayPHP\Resources\Metadata|null $metadata
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Authorization
     */
    public function authorize(
        BasePaymentType $paymentType,
        float $amount,
        ?string $returnUrl = null,
        ?Customer $customer = null,
        ?Metadata $metadata = null
    ): Authorization;
}
