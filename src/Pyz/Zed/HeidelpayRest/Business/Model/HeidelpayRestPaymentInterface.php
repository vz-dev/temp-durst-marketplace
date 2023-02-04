<?php
/**
 * Durst - project - HeidelpayRestInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.01.19
 * Time: 14:59
 */

namespace Pyz\Zed\HeidelpayRest\Business\Model;

use Generated\Shared\Transfer\HeidelpayRestPaymentTransfer;

interface HeidelpayRestPaymentInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer
     */
    public function getHeidelpayRestPaymentByIdSalesOrder(int $idSalesOrder): HeidelpayRestPaymentTransfer;

    /**
     * @param string $orderRef
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer
     */
    public function getHeidelpayRestPaymentBySalesOrderRef(string $orderRef): HeidelpayRestPaymentTransfer;

    /**
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $heidelpayRestPaymentTransfer
     *
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\InvalidArgumentException
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer
     */
    public function addHeidelpayRestPayment(HeidelpayRestPaymentTransfer $heidelpayRestPaymentTransfer): HeidelpayRestPaymentTransfer;

    /**
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $heidelpayRestPaymentTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\InvalidArgumentException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function updateHeidelpayRestPayment(HeidelpayRestPaymentTransfer $heidelpayRestPaymentTransfer): void;

    /**
     * @param string $paymentId
     *
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\PaymentNotFoundException
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer
     */
    public function getHeidelpayRestPaymentByPaymentId(string $paymentId): HeidelpayRestPaymentTransfer;
}
