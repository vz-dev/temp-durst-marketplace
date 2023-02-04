<?php
/**
 * Durst - merchant_center - InvoiceManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.08.18
 * Time: 15:05
 */

namespace Pyz\Zed\Oms\Business\Model\Mail;


use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface InvoiceMailManagerInterface
{
    /**
     * @param OrderTransfer $orderTransfer
     * @param BranchTransfer $branchTransfer
     * @param string $mailType
     * @param \Generated\Shared\Transfer\MailTransfer|null $mailTransfer
     * @return void
     */
    public function sendMail(OrderTransfer $orderTransfer, BranchTransfer $branchTransfer, string $mailType, ?MailTransfer $mailTransfer = null);

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    public function getSplitExpensesRefundsReturnDepositsFromOrder(OrderTransfer $orderTransfer)  : array;
}
