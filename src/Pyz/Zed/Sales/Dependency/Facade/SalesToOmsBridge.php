<?php
/**
 * Durst - project - SalesToOmsBridge.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-25
 * Time: 15:00
 */

namespace Pyz\Zed\Sales\Dependency\Facade;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Spryker\Zed\Sales\Dependency\Facade\SalesToOmsBridge as SprykerSalesToOmsBridge;

class SalesToOmsBridge extends SprykerSalesToOmsBridge
{
    /**
     * @var OmsFacadeInterface
     */
    protected $omsFacade;

    /**
     * @param OrderTransfer $orderTransfer
     * @param BranchTransfer $branchTransfer
     * @param string $mailType
     */
    public function sendInvoiceMail(OrderTransfer $orderTransfer, BranchTransfer $branchTransfer, string $mailType)
    {
        $this->omsFacade->sendInvoiceMail($orderTransfer, $branchTransfer, $mailType);
    }

    /**
     * @return array
     */
    public function getOrderItemMatrix(): array
    {
        return $this->omsFacade->getOrderItemMatrix();
    }
}
