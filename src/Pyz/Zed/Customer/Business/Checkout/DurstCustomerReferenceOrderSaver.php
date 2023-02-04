<?php

namespace Pyz\Zed\Customer\Business\Checkout;

use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class DurstCustomerReferenceOrderSaver implements DurstCustomerReferenceOrderSaverInterface
{
    /**
     * @var SalesQueryContainerInterface
     */
    protected $salesQueryContainer;

    /**
     * @param SalesQueryContainerInterface $salesQueryContainer
     */
    public function __construct(SalesQueryContainerInterface $salesQueryContainer)
    {
        $this->salesQueryContainer = $salesQueryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param SaveOrderTransfer $orderTransfer
     * @param string $durstCustomerReference
     * @return void
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function saveDurstCustomerReference(SaveOrderTransfer $orderTransfer, string $durstCustomerReference): void
    {
        $salesOrderEntity = $this->getOrderEntity($orderTransfer->getIdSalesOrder());
        $salesOrderEntity->setDurstCustomerReference($durstCustomerReference);
        $salesOrderEntity->save();
    }

    /**
     * @param int $orderId
     * @return SpySalesOrder
     * @throws AmbiguousComparisonException
     */
    protected function getOrderEntity(int $orderId) : SpySalesOrder
    {
        return $this
            ->salesQueryContainer
            ->querySalesOrder()
            ->filterByIdSalesOrder($orderId)
            ->findOne();
    }
}
