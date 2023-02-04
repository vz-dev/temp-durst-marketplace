<?php
/**
 * Durst - project - CardInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.01.19
 * Time: 13:53
 */

namespace Pyz\Zed\HeidelpayRest\Business\PaymentType;

use heidelpayPHP\Resources\Customer;
use heidelpayPHP\Resources\Metadata;
use heidelpayPHP\Resources\PaymentTypes\Card;
use heidelpayPHP\Resources\TransactionTypes\Authorization;

interface CardTypeInterface
{
    /**
     * @param \heidelpayPHP\Resources\PaymentTypes\Card $paymentType
     * @param float $amount
     * @param string|null $returnUrl
     * @param \heidelpayPHP\Resources\Customer|null $customer
     * @param \heidelpayPHP\Resources\Metadata|null $metadata
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Authorization
     */
    public function authorize(
        Card $paymentType,
        float $amount,
        ?string $returnUrl = null,
        ?Customer $customer = null,
        ?Metadata $metadata = null
    ): Authorization;
}
