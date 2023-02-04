<?php
/**
 * Durst - project - SalesToOmsInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-25
 * Time: 15:00
 */

namespace Pyz\Zed\Sales\Dependency\Facade;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Sales\Dependency\Facade\SalesToOmsInterface as SprykerSalesToOmsInterface;

interface SalesToOmsInterface extends SprykerSalesToOmsInterface
{

    /**
     * @param OrderTransfer $orderTransfer
     * @param BranchTransfer $branchTransfer
     * @param string $mailType
     * @return void
     */
    public function sendInvoiceMail(OrderTransfer $orderTransfer, BranchTransfer $branchTransfer, string $mailType);

    /**
     * @return array
     */
    public function getOrderItemMatrix(): array;
}
