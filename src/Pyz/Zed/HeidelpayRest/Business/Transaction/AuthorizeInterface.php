<?php
/**
 * Durst - project - AuthorizeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 16:35
 */

namespace Pyz\Zed\HeidelpayRest\Business\Transaction;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface AuthorizeInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function cancelAuthorization(int $idSalesOrder): void;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     */
    public function authorizeOrder(OrderTransfer $orderTransfer): void;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isAuthorizationCompleted(OrderTransfer $orderTransfer): bool;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function postSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer;

    /**
     * @param string $paymentId
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    public function getAuthorizationStatusByPaymentId(string $paymentId): HeidelpayRestAuthorizationTransfer;

    /**
     * @param string $orderRef
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer
     */
    public function getAuthorizationStatusBySalesOrderRef(string $orderRef): HeidelpayRestAuthorizationTransfer;
}
