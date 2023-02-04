<?php
/**
 * Durst - project - RefundMailManagerInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-02-21
 * Time: 13:04
 */

namespace Pyz\Zed\Oms\Business\Model\Mail;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface RefundMailManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @param \Generated\Shared\Transfer\DriverTransfer $tourTransfer
     *
     * @return void
     */
    public function sendMail(OrderTransfer $orderTransfer, BranchTransfer $branchTransfer, DriverTransfer $driverTransfer);
}
