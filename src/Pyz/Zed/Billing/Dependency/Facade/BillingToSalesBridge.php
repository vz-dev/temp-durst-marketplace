<?php
/**
 * Durst - project - BillingToSalesBridge.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-23
 * Time: 07:09
 */

namespace Pyz\Zed\Billing\Dependency\Facade;


use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;

class BillingToSalesBridge implements BillingToSalesBridgeInterface
{
    /**
     * @var SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * BillingToSalesBridge constructor.
     * @param SalesFacadeInterface $salesFacade
     */
    public function __construct(SalesFacadeInterface $salesFacade)
    {
        $this->salesFacade = $salesFacade;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param int $branchId
     * @return OrderTransfer[]
     */
    public function getOrdersByInvoiceDateBetweenStartAndEndDateForBranchId(string $startDate, string $endDate, int $branchId): array
    {
        return $this
            ->salesFacade
            ->getOrdersByInvoiceDateBetweenStartAndEndDateForBranchId($startDate, $endDate, $branchId);
    }

}
