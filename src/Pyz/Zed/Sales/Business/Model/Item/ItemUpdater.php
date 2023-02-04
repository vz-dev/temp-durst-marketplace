<?php

namespace Pyz\Zed\Sales\Business\Model\Item;

use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Sales\Business\Exception\OrderItemNotFoundException;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;

class ItemUpdater implements ItemUpdaterInterface
{
    /**
     * @var SalesQueryContainerInterface
     */
    protected $queryContainer;

    public function __construct(SalesQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param array $idSalesOrderItems
     * @param bool $value
     */
    public function setOrderItemsStuck(array $idSalesOrderItems, bool $value): void
    {
        $orderItems = $this
            ->queryContainer
            ->querySalesOrderItem()
            ->filterByIdSalesOrderItem_In($idSalesOrderItems)
            ->find();

        foreach ($orderItems as $orderItem) {
            $orderItem->setIsStuck($value);
        }

        $orderItems->save();
    }

    /**
     * @param int $idSalesOrderItem
     * @param string $value
     * @throws PropelException
     */
    public function setOrderItemDeliveryStatus(int $idSalesOrderItem, string $value): void
    {
        $orderItem = $this
            ->queryContainer
            ->querySalesOrderItem()
            ->findOneByIdSalesOrderItem($idSalesOrderItem);

        if ($orderItem === null) {
            throw new OrderItemNotFoundException($idSalesOrderItem);
        }

        $orderItem
            ->setDeliveryStatus($value)
            ->save();
    }
}
