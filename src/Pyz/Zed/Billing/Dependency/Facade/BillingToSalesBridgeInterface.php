<?php
/**
 * Durst - project - BillingToSalesBridgeInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-23
 * Time: 07:09
 */

namespace Pyz\Zed\Billing\Dependency\Facade;


use Generated\Shared\Transfer\OrderTransfer;

interface BillingToSalesBridgeInterface
{
    /**
     * @param string $startDate
     * @param string $endDate
     * @param int $branchId
     * @return OrderTransfer[]
     */
    public function getOrdersByInvoiceDateBetweenStartAndEndDateForBranchId(string $startDate, string $endDate, int $branchId): array;
}
