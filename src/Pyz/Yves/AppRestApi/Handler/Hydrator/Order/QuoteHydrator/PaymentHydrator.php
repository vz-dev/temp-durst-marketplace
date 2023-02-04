<?php
/**
 * Durst - project - PaymentHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.05.18
 * Time: 13:04
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator;

use Generated\Shared\Transfer\HeidelpayRestPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RetailPaymentTransfer;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Shared\RetailPayment\RetailPaymentConfig;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OrderKeyRequestInterface as Request;
use stdClass;

class PaymentHydrator implements QuoteHydratorInterface
{
    const DUMMY_PAYMENT_TYPE_ID = 's-dummy-abcd12345678';

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \stdClass $requestObject
     *
     * @return void
     */
    public function hydrateQuote(QuoteTransfer $quoteTransfer, stdClass $requestObject)
    {
        $paymentTransfer = $this->hydratePayment($requestObject->{Request::KEY_PAYMENT}, $quoteTransfer->getTotals()->getGrandTotal());

        $quoteTransfer->setPayment($paymentTransfer);
        $quoteTransfer->addPayment($paymentTransfer);
    }

    /**
     * @param $payment
     * @param int $amount
     *
     * @return \Generated\Shared\Transfer\PaymentTransfer
     */
    protected function hydratePayment($payment, int $amount): PaymentTransfer
    {
        if (in_array($payment->{Request::KEY_PAYMENT_METHOD}, HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHODS) === true) {
            return $this
                ->hydrateWholesalePayment($payment, $amount);
        }

        return $this
            ->hydrateRetailPayment($payment, $amount);
    }

    /**
     * @param $payment
     * @param int $amount
     *
     * @return \Generated\Shared\Transfer\PaymentTransfer
     */
    protected function hydrateRetailPayment(
        $payment,
        int $amount
    ): PaymentTransfer {
        $paymentTransfer = $this->createPaymentTransfer();

        $retailPayment = new RetailPaymentTransfer();
        $paymentTransfer->setRetailPayment($retailPayment);
        $paymentTransfer->setPaymentSelection($payment->{Request::KEY_PAYMENT_METHOD});
        $paymentTransfer->setPaymentMethod($payment->{Request::KEY_PAYMENT_METHOD});
        $paymentTransfer->setPaymentProvider(RetailPaymentConfig::PROVIDER_NAME);
        $paymentTransfer->setAmount($amount);

        return $paymentTransfer;
    }

    /**
     * @param $payment
     * @param int $amount
     *
     * @return \Generated\Shared\Transfer\PaymentTransfer
     */
    protected function hydrateWholesalePayment(
        $payment,
        int $amount
    ): PaymentTransfer {
        $paymentTransfer = $this->createPaymentTransfer();

        $heidelpayRestPayment = (new HeidelpayRestPaymentTransfer());

        if (isset($payment->{Request::KEY_PAYMENT_PAYMENT_TYPE_ID}) === true) {
            $heidelpayRestPayment->setPaymentTypeId($payment->{Request::KEY_PAYMENT_PAYMENT_TYPE_ID});
        } else {
            if (isset($payment->{Request::KEY_PAYMENT_METHOD}) &&
                in_array(
                    $payment->{Request::KEY_PAYMENT_METHOD},
                    [
                        HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_CASH_ON_DELIVERY,
                        HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_CREDIT_CARD_ON_DELIVERY,
                        HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_EC_CARD_ON_DELIVERY
                    ]
                )
            ) {
                $heidelpayRestPayment->setPaymentTypeId(self::DUMMY_PAYMENT_TYPE_ID);
            }
        }

        if (isset($payment->{Request::KEY_PAYMENT_RETURN_URL}) !== false) {
            $heidelpayRestPayment
                ->setReturnUrl($payment->{Request::KEY_PAYMENT_RETURN_URL});
        }

        $paymentTransfer->setHeidelpayRestPayment($heidelpayRestPayment);
        $paymentTransfer->setPaymentSelection($payment->{Request::KEY_PAYMENT_METHOD});
        $paymentTransfer->setPaymentMethod($payment->{Request::KEY_PAYMENT_METHOD});
        $paymentTransfer->setPaymentProvider(HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_PROVIDER);
        $paymentTransfer->setAmount($amount);

        return $paymentTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentTransfer
     */
    protected function createPaymentTransfer(): PaymentTransfer
    {
        return new PaymentTransfer();
    }
}
