<?php
/**
 * Durst - project - OrderHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.07.18
 * Time: 11:44
 */

namespace Pyz\Zed\Sales\Business\Model\Order;

use Exception;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\TaxRateTotalTransfer;
use Generated\Shared\Transfer\TaxTotalTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Tax\Persistence\SpySalesOrderTaxRateTotal;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;
use Spryker\Zed\Sales\Business\Model\Order\OrderHydrator as SprykerOrderHydrator;
use Spryker\Zed\Sales\Dependency\Facade\SalesToOmsInterface;
use Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface;

class OrderHydrator extends SprykerOrderHydrator implements OrderHydratorInterface
{
    /**
     * @var \Pyz\Zed\Sales\Dependency\Plugin\DeflateOrderPluginInterface[]
     */
    protected $deflaterPluginStack;

    /**
     * OrderHydrator constructor.
     *
     * @param \Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Sales\Dependency\Facade\SalesToOmsInterface $omsFacade
     * @param array $hydrateOrderPlugins
     * @param array $deflaterPlugins
     */
    public function __construct(
        SalesQueryContainerInterface $queryContainer,
        SalesToOmsInterface $omsFacade,
        array $hydrateOrderPlugins = [],
        array $deflaterPlugins = []
    ) {
        parent::__construct($queryContainer, $omsFacade, $hydrateOrderPlugins);
        $this->deflaterPluginStack = $deflaterPlugins;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    protected function hydrateExpensesToOrderTransfer(SpySalesOrder $orderEntity, OrderTransfer $orderTransfer)
    {
        foreach ($orderEntity->getExpenses() as $expenseEntity) {
            $expenseTransfer = new ExpenseTransfer();
            $expenseTransfer->fromArray($expenseEntity->toArray(), true);

            $expenseTransfer->setQuantity($expenseEntity->getQuantity());
            $expenseTransfer->setUnitGrossPrice($expenseEntity->getGrossPrice());
            $expenseTransfer->setUnitNetPrice($expenseEntity->getNetPrice());
            $expenseTransfer->setUnitPrice($expenseEntity->getPrice());
            $expenseTransfer->setUnitPriceToPayAggregation($expenseEntity->getPriceToPayAggregation());
            $expenseTransfer->setUnitTaxAmount($expenseEntity->getTaxAmount());
            $expenseTransfer->setUnitDiscountAmountAggregation($expenseEntity->getDiscountAmountAggregation());
            $expenseTransfer->setSumPrice($expenseEntity->getQuantity() * $expenseEntity->getPrice());
            $expenseTransfer->setSumPriceToPayAggregation($expenseEntity->getQuantity() * $expenseEntity->getPriceToPayAggregation());

            $orderTransfer->addExpense($expenseTransfer);
        }
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    protected function hydrateOrderTotals(SpySalesOrder $orderEntity, OrderTransfer $orderTransfer)
    {
        $salesOrderTotalsEntity = $orderEntity->getLastOrderTotals();

        if (!$salesOrderTotalsEntity) {
            return;
        }

        $totalsTransfer = new TotalsTransfer();

        $taxTotalTransfer = new TaxTotalTransfer();
        $taxTotalTransfer->setAmount($salesOrderTotalsEntity->getTaxTotal());
        $totalsTransfer->setTaxTotal($taxTotalTransfer);

        $totalsTransfer->setExpenseTotal($salesOrderTotalsEntity->getOrderExpenseTotal());
        $totalsTransfer->setRefundTotal($salesOrderTotalsEntity->getRefundTotal());
        $totalsTransfer->setGrandTotal($salesOrderTotalsEntity->getGrandTotal());
        $totalsTransfer->setSubtotal($salesOrderTotalsEntity->getSubtotal());
        $totalsTransfer->setDiscountTotal($salesOrderTotalsEntity->getDiscountTotal());
        $totalsTransfer->setCanceledTotal($salesOrderTotalsEntity->getCanceledTotal());
        $totalsTransfer->setGrossSubtotal($salesOrderTotalsEntity->getGrossSubtotal());
        $totalsTransfer->setDepositTotal($salesOrderTotalsEntity->getDepositTotal());
        $totalsTransfer->setDeliveryCostTotal($salesOrderTotalsEntity->getDeliveryCostTotal());
        $totalsTransfer->setDisplayTotal($salesOrderTotalsEntity->getDisplayTotal());
        $totalsTransfer->setWeightTotal($salesOrderTotalsEntity->getWeightTotal());

        foreach ($salesOrderTotalsEntity->getSpySalesOrderTaxRateTotals() as $spySalesOrderTaxRateTotal) {
            $totalsTransfer->addTaxRateTotals($this->taxRateTotalEntityToTransfer($spySalesOrderTaxRateTotal));
        }

        $orderTransfer->setTotals($totalsTransfer);
    }

    /**
     * @param \Orm\Zed\Tax\Persistence\SpySalesOrderTaxRateTotal $entity
     *
     * @return \Generated\Shared\Transfer\TaxRateTotalTransfer
     */
    protected function taxRateTotalEntityToTransfer(SpySalesOrderTaxRateTotal $entity): TaxRateTotalTransfer
    {
        return (new TaxRateTotalTransfer())
            ->setAmount($entity->getTaxTotal())
            ->setRate($entity->getTaxRate());
    }

    /**
     * @param $idSalesOrder
     * @param $branchId
     *
     * @throws \Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrateOrderTransferFromPersistenceByIdSalesOrderAndBranchId($idSalesOrder, $branchId)
    {
        $orderEntity = $this->queryContainer
            ->querySalesOrderDetails($idSalesOrder)
            ->filterByFkBranch($branchId)
            ->findOne();

        if ($orderEntity === null) {
            throw new InvalidSalesOrderException(
                sprintf(
                    'Order could not be found for ID %s - BranchId %s',
                    $idSalesOrder,
                    $branchId
                )
            );
        }

        $this->queryContainer->fillOrderItemsWithLatestStates($orderEntity->getItems());

        return $this->createOrderTransfer($orderEntity);
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createOrderTransfer(SpySalesOrder $orderEntity): OrderTransfer
    {
        $orderTransfer = parent::createOrderTransfer($orderEntity);
        if ($orderEntity->getSignedAt() !== null) {
            $orderTransfer->setSignedAt($orderEntity->getSignedAt()->getTimestamp());
        }

        try {
            if ($orderEntity->getDstCancelOrders()->count() > 0) {
                /* @var $cancelEntity \Orm\Zed\CancelOrder\Persistence\DstCancelOrder */
                $cancelEntity = $orderEntity
                    ->getDstCancelOrders()
                    ->offsetGet(0);
                $orderTransfer
                    ->setCancelAt(
                        $cancelEntity
                            ->getCreatedAt()
                    );
            }
        } catch (Exception $exception) {
            // do nothing
        }

        return $orderTransfer;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param int $idBranch
     *
     * @return array
     */
    public function hydrateOrderTransfersFromPersistenceByInvoiceDateBetweenStartAndEndDateForBranchId(string $startDate, string $endDate, int $idBranch)
    {
        $orderTransfers = [];

        $orderEntities = $this->queryContainer
            ->querySalesOrder()
            ->filterByFkBranch($idBranch)
            ->filterByInvoiceCreatedAt($startDate . ' 00:00:00', Criteria::GREATER_EQUAL)
            ->filterByInvoiceCreatedAt($endDate . ' 23:59:59', Criteria::LESS_EQUAL)
            ->useDstBillingItemQuery(null, Criteria::LEFT_JOIN)
                 ->filterByFkSalesOrder(null, Criteria::ISNULL) // only get orders for which a billing item has not yet been created
            ->endUse()
            ->find();

        foreach ($orderEntities as $orderEntity) {
            $orderEntityWDetails = $this->queryContainer
                ->querySalesOrderDetails($orderEntity->getIdSalesOrder())
                ->filterByFkBranch($idBranch)
                ->findOne();

            $this->queryContainer->fillOrderItemsWithLatestStates($orderEntityWDetails->getItems());

            $orderTransfers[] = $this->createOrderTransfer($orderEntityWDetails);
        }

        return $orderTransfers;
    }

    /**
     * @param int $idSalesOrder
     * @param int $branchId
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrateDeflatedOrderTransferFromPersistenceByIdSalesOrderAndBranchId(int $idSalesOrder, int $branchId)
    {
        $orderTransfer = $this->hydrateOrderTransferFromPersistenceByIdSalesOrderAndBranchId($idSalesOrder, $branchId);

        return $this->runDeflatorPluginStack($orderTransfer);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrateDeflatedOrderTransferFromPersistenceByIdSalesOrder(int $idSalesOrder) : OrderTransfer
    {
        $orderTransfer = $this->hydrateOrderTransferFromPersistenceByIdSalesOrder($idSalesOrder);

        return $this->runDeflatorPluginStack($orderTransfer);
    }

    /**
     * @param array $idSalesOrders
     *
     * @return array|OrderTransfer[]
     *
     * @throws PropelException
     */
    public function hydrateMultipleOrderTransfersByIdSalesOrders(array $idSalesOrders): array
    {
        $orderEntities = $this
            ->queryContainer
            ->querySalesOrder()
            ->filterByIdSalesOrder_In($idSalesOrders)
            ->find();

        $orderTransfers = [];

        foreach ($orderEntities as $orderEntity) {
            $orderTransfers[] = $this->createOrderTransfer($orderEntity);
        }

        return $orderTransfers;
    }

    /**
     * @param array $orderReferences
     *
     * @return OrderTransfer[]|array
     *
     * @throws PropelException
     */
    public function hydrateMultipleOrderTransfersWithTotalsByOrderReferences(array $orderReferences): array
    {
        $orderEntities = $this
            ->queryContainer
            ->querySalesOrder()
            ->joinWithOrderTotal()
            ->filterByOrderReference_In($orderReferences)
            ->find();

        $orderTransfers = [];

        foreach ($orderEntities as $orderEntity) {
            $this->queryContainer->fillOrderItemsWithLatestStates($orderEntity->getItems());

            $orderTransfers[] = $this->createOrderTransfer($orderEntity);
        }

        return $orderTransfers;
    }

    /**
     * @param string $orderReference
     *
     * @return OrderTransfer
     *
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function hydrateOrderTransferFromPersistenceByOrderReference(string $orderReference): OrderTransfer
    {
        $orderEntity = $this
            ->queryContainer
            ->querySalesOrder()
            ->findOneByOrderReference($orderReference);

        if ($orderEntity === null) {
            throw new InvalidSalesOrderException(
                sprintf(
                    'Order could not be found for reference "%s"',
                    $orderReference
                )
            );
        }

        $this
            ->queryContainer
            ->fillOrderItemsWithLatestStates($orderEntity->getItems());

        $orderTransfer = $this->createOrderTransfer($orderEntity);

        return $orderTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function runDeflatorPluginStack(OrderTransfer $orderTransfer)
    {
        foreach ($this->deflaterPluginStack as $deflater) {
            $deflater->deflate($orderTransfer);
        }

        return $orderTransfer;
    }
}
