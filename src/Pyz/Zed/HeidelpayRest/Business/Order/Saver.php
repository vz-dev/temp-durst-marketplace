<?php
/**
 * Durst - project - Saver.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 09:33
 */

namespace Pyz\Zed\HeidelpayRest\Business\Order;

use Generated\Shared\Transfer\HeidelpayRestPaymentTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface;

class Saver implements SaverInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface
     */
    protected $heidelpayRestPayment;

    /**
     * Saver constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface $heidelpayRestPayment
     */
    public function __construct(
        HeidelpayRestPaymentInterface $heidelpayRestPayment
    ) {
        $this->heidelpayRestPayment = $heidelpayRestPayment;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     */
    public function saveOrderPayment(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): void {
        if ($quoteTransfer->getPayment()->getRetailPayment() !== null &&
        $quoteTransfer->getPayment()->getHeidelpayRestPayment() === null) {
            return;
        }

        $quoteTransfer->requirePayments();
        $saveOrderTransfer->requireIdSalesOrder();

        foreach ($quoteTransfer->getPayments() as $payment) {
            $this->assertPaymentRequirements($payment);

            $heidelpayPayment = $payment->getHeidelpayRestPayment();
            $paymentTransfer = $this->preparePaymentTransfer(
                $heidelpayPayment->getPaymentTypeId(),
                $saveOrderTransfer->getIdSalesOrder()
            );
            $paymentTransfer->setReturnUrl($heidelpayPayment->getReturnUrl());

            $this->heidelpayRestPayment->addHeidelpayRestPayment($paymentTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentTransfer $paymentTransfer
     *
     * @return void
     */
    protected function assertPaymentRequirements(PaymentTransfer $paymentTransfer): void
    {
        $paymentTransfer
            ->requireHeidelpayRestPayment();
    }

    /**
     * @param string $paymentTypeId
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer
     */
    protected function preparePaymentTransfer(string $paymentTypeId, int $idSalesOrder): HeidelpayRestPaymentTransfer
    {
        return (new HeidelpayRestPaymentTransfer())
            ->setPaymentTypeId($paymentTypeId)
            ->setFkSalesOrder($idSalesOrder);
    }
}
