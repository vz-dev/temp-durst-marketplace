<?php
/**
 * Durst - project - HeidelpayRestToSalesBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 31.01.19
 * Time: 14:36
 */

namespace Pyz\Zed\HeidelpayRest\Dependency\Facade;

use Generated\Shared\Transfer\OrderDetailsCommentsTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;

class HeidelpayRestToSalesBridge implements HeidelpayRestToSalesBridgeInterface
{
    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * HeidelpayRestToSalesBridge constructor.
     *
     * @param \Pyz\Zed\Sales\Business\SalesFacadeInterface $salesFacade
     */
    public function __construct(SalesFacadeInterface $salesFacade)
    {
        $this->salesFacade = $salesFacade;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getOrderByIdSalesOrder(int $idSalesOrder): OrderTransfer
    {
        return $this
            ->salesFacade
            ->getOrderByIdSalesOrder($idSalesOrder);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function getDeflatedOrderByIdSalesOrder(int $idSalesOrder): OrderTransfer
    {
        return $this
            ->salesFacade
            ->getDeflatedOrderByIdSalesOrder($idSalesOrder);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderDetailsCommentsTransfer
     */
    public function getCustomerOrderCommentsByIdSalesOrder(int $idSalesOrder): OrderDetailsCommentsTransfer
    {
        return $this
            ->salesFacade
            ->getCustomerOrderCommentsByIdSalesOrder($idSalesOrder);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return bool
     */
    public function incrementSalesOrderRetryCounter(int $idSalesOrder): bool
    {
        return $this
            ->salesFacade
            ->incrementSalesOrderRetryCounter(
                $idSalesOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return bool
     */
    public function resetSalesOrderRetryCounter(int $idSalesOrder): bool
    {
        return $this
            ->salesFacade
            ->resetSalesOrderRetryCounter(
                $idSalesOrder
            );
    }

    /**
     * @param int $idSalesOrder
     * @return string[]
     */
    public function getDistinctOrderStates(int $idSalesOrder): array
    {
        return $this
            ->salesFacade
            ->getDistinctOrderStates(
                $idSalesOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param bool $state
     * @return bool
     */
    public function updateHeidelpayCustomerState(
        int $idSalesOrder,
        bool $state
    ): bool
    {
        return $this
            ->salesFacade
            ->updateHeidelpayCustomerState(
                $idSalesOrder,
                $state
            );
    }
}
