<?php
/**
 * Durst - project - PaymentType.php.
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
use heidelpayPHP\Resources\PaymentTypes\Card;
use heidelpayPHP\Resources\PaymentTypes\Paypal;
use heidelpayPHP\Resources\TransactionTypes\Authorization;
use Pyz\Zed\HeidelpayRest\Business\Exception\InvalidPaymentMethodException;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestBusinessFactory;

class PaymentType implements PaymentTypeInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestBusinessFactory
     */
    protected $factory;

    /**
     * PaymentType constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestBusinessFactory $factory
     */
    public function __construct(HeidelpayRestBusinessFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritDoc}
     *
     * @param \heidelpayPHP\Resources\PaymentTypes\BasePaymentType $paymentType
     * @param float $amount
     * @param null|string $returnUrl
     * @param \heidelpayPHP\Resources\Customer|null $customer
     * @param \heidelpayPHP\Resources\Metadata|null $metadata
     *
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\InvalidPaymentMethodException
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Authorization
     */
    public function authorize(
        BasePaymentType $paymentType,
        float $amount,
        ?string $returnUrl = null,
        ?Customer $customer = null,
        ?Metadata $metadata = null
    ): Authorization {
        if ($paymentType instanceof Card) {
            /** @var \heidelpayPHP\Resources\PaymentTypes\Card $paymentType */
            return $this
                ->factory
                ->createCardPaymentType()
                ->authorize($paymentType, $amount, $returnUrl, $customer, $metadata);
        }

        if ($paymentType instanceof Paypal) {
            /** @var \heidelpayPHP\Resources\PaymentTypes\Paypal $paymentType */
            return $this
                ->factory
                ->createPayPalPaymentType()
                ->authorize($paymentType, $amount, $returnUrl, $customer, $metadata);
        }

        throw new InvalidPaymentMethodException(
            sprintf(
                InvalidPaymentMethodException::MESSAGE,
                get_class($paymentType)
            )
        );
    }
}
