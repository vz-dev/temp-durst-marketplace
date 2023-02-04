<?php
/**
 * Durst - project - DepositAmountOrderItemHydrator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-05-07
 * Time: 14:38
 */

namespace Pyz\Zed\Deposit\Business\Hydrator;


use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;


class DepositAmountOrderItemHydrator
{
    /**
     * @var SalesQueryContainerInterface
     */
    protected $salesQueryContainer;


    /**
     * DepositAmountOrderItemHydrator constructor.
     * @param SalesQueryContainerInterface $salesQueryContainer
     */
    public function __construct(SalesQueryContainerInterface $salesQueryContainer)
    {
        $this->salesQueryContainer = $salesQueryContainer;
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function hydrateDepositAmountOrderItem(OrderTransfer $orderTransfer) : OrderTransfer
    {
        $orderEntity = $this
            ->salesQueryContainer
            ->querySalesOrder()
            ->filterByIdSalesOrder($orderTransfer->getIdSalesOrder())
            ->findOne();

        foreach ($orderEntity->getItems() as $item){
            foreach ($orderTransfer->getItems() as $transferItem){
                if($item->getIdSalesOrderItem() == $transferItem->getIdSalesOrderItem()){
                    $transferItem->setUnitDeposit($item->getDepositAmount());
                    break;
                }
            }
        }

        return $orderTransfer;
    }
}