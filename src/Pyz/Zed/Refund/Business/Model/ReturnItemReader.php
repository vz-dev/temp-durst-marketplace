<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-20
 * Time: 13:46
 */

namespace Pyz\Zed\Refund\Business\Model;


use Generated\Shared\Transfer\OrderTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Shared\Sales\SalesConstants;
use Pyz\Zed\Refund\Persistence\RefundQueryContainerInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class ReturnItemReader implements ReturnItemReaderInterface
{

    /**
     * @var RefundQueryContainerInterface
     */
    protected $refundQueryContainer;

    /**
     * @var SalesQueryContainerInterface
     */
    protected $salesQueryContainer;

    /**
     * ReturnItemReader constructor.
     * @param RefundQueryContainerInterface $refundQueryContainer
     * @param SalesQueryContainerInterface $salesQueryContainer
     */
    public function __construct(
        RefundQueryContainerInterface $refundQueryContainer,
        SalesQueryContainerInterface $salesQueryContainer
    ) {
        $this->refundQueryContainer = $refundQueryContainer;
        $this->salesQueryContainer = $salesQueryContainer;
    }

    /**
     * {@inheritdoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateSalesOrderReturnItemFlag(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $orderTransfer
            ->setHasReturnItem(
                $this
                ->refundQueryContainer
                ->queryRefundBySalesOrderId(
                    $orderTransfer->getIdSalesOrder()
                )
                ->count() > 0
            );
    }

    /**
     * {@inheritdoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     * @throws AmbiguousComparisonException
     */
    public function hydrateSalesOrderOtherThanMissingReturnItemFlag(OrderTransfer $orderTransfer): OrderTransfer
    {
        return $orderTransfer
            ->setHasOtherThanMissingReturnItem(
                $this
                    ->salesQueryContainer
                    ->querySalesOrderById($orderTransfer->getIdSalesOrder())
                    ->useItemQuery()
                        ->filterByDeliveryStatus(
                            [
                                SalesConstants::ORDER_ITEM_DELIVERY_STATUS_DELIVERED,
                                SalesConstants::ORDER_ITEM_DELIVERY_STATUS_MISSING,
                                SalesConstants::ORDER_ITEM_DELIVERY_STATUS_CANCELLED
                            ],
                            Criteria::NOT_IN
                        )
                    ->endUse()
                    ->count() > 0
            );
    }
}
