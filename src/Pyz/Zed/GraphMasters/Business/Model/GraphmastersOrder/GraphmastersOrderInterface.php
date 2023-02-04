<?php

namespace Pyz\Zed\GraphMasters\Business\Model\GraphmastersOrder;

use Generated\Shared\Transfer\DriverAppTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\GraphMastersOrderTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\Exception\EntityNotFoundException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

interface GraphmastersOrderInterface
{
    /**
     * @param GraphMastersOrderTransfer $orderTransfer
     *
     * @return DstGraphmastersOrder
     *
     * @throws PropelException
     */
    public function save(GraphMastersOrderTransfer $orderTransfer): DstGraphmastersOrder;

    /**
     * @param DstGraphmastersOrder $orderEntity
     *
     * @return GraphMastersOrderTransfer
     */
    public function entityToTransfer(DstGraphmastersOrder $orderEntity): GraphMastersOrderTransfer;

    /**
     * @param string $fkOrderReference
     *
     * @return GraphMastersOrderTransfer
     *
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function getOrderByFkOrderReference(string $fkOrderReference): GraphMastersOrderTransfer;

    /**
     * @param array $fkOrderReferences
     *
     * @return GraphMastersOrderTransfer[]|array
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function getMultipleOrdersByFkOrderReferences(array $fkOrderReferences): array;

    /**
     * @param string $fkOrderReference
     */
    public function delete(string $fkOrderReference): void;

    /**
     * @param string $orderReference
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function markOrderFinishedByReference(string $orderReference): void;

    /**
     * @param string $orderReference
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function markOrderCancelledByReference(string $orderReference): void;

    /**
     * @param string $orderReference
     *
     * @return bool
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function isOrderMarkedCancelled(string $orderReference): bool;
}
