<?php
/**
 * Durst - project - AccountingToSalesBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.03.20
 * Time: 17:40
 */

namespace Pyz\Zed\Accounting\Dependency\Facade;


use DateTime;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;

class AccountingToSalesBridge implements AccountingToSalesBridgeInterface
{
    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * AccountingToSalesBridge constructor.
     * @param \Pyz\Zed\Sales\Business\SalesFacadeInterface $salesFacade
     */
    public function __construct(
        SalesFacadeInterface $salesFacade
    )
    {
        $this->salesFacade = $salesFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param array $states
     * @param \DateTime $start
     * @param \DateTime $end
     * @return \Generated\Shared\Transfer\SpySalesOrderItemEntityTransfer[]
     */
    public function getOrderItemsByBranchAndStateAndDateRange(int $idBranch, array $states, DateTime $start, DateTime $end): array
    {
        return $this
            ->salesFacade
            ->getOrderItemsByBranchAndStateAndDateRange(
                $idBranch,
                $states,
                $start,
                $end
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string[] $stateNames
     * @return int[]
     */
    public function getStateIdsByStateNames(array $stateNames): array
    {
        return $this
            ->salesFacade
            ->getStateIdsByStateNames(
                $stateNames
            );
    }
}
