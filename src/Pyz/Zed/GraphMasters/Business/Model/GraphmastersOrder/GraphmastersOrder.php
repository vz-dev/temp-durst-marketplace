<?php

namespace Pyz\Zed\GraphMasters\Business\Model\GraphmastersOrder;

use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\GraphMastersApiGeoLocationTransfer;
use Generated\Shared\Transfer\GraphMastersApiOrderUpdateTransfer;
use Generated\Shared\Transfer\GraphMastersOrderTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Pyz\Zed\GraphMasters\Business\Exception\EntityNotFoundException;
use Pyz\Zed\GraphMasters\Business\Handler\OrderHandlerInterface;
use Pyz\Zed\GraphMasters\Business\Model\GraphMastersSettingsInterface;
use Pyz\Zed\GraphMasters\GraphMastersConfig;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;

class GraphmastersOrder implements GraphmastersOrderInterface
{
    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var GraphMastersConfig
     */
    protected $config;

    /**
     * @var GraphMastersSettingsInterface
     */
    protected $graphMastersSettingsModel;

    /**
     * @var SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var OrderHandlerInterface
     */
    protected $orderHandler;

    /**
     * @param GraphMastersQueryContainerInterface $queryContainer
     * @param GraphMastersConfig $config
     * @param SalesFacadeInterface $salesFacade
     */
    public function __construct(
        GraphMastersQueryContainerInterface $queryContainer,
        GraphMastersConfig $config,
        GraphMastersSettingsInterface $graphMastersSettingsModel,
        SalesFacadeInterface $salesFacade,
        OrderHandlerInterface $orderHandler
    ) {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->graphMastersSettingsModel = $graphMastersSettingsModel;
        $this->salesFacade = $salesFacade;
        $this->orderHandler = $orderHandler;
    }

    /**
     * @param GraphMastersOrderTransfer $orderTransfer
     *
     * @return DstGraphmastersOrder
     *
     * @throws PropelException
     */
    public function save(GraphMastersOrderTransfer $orderTransfer): DstGraphmastersOrder
    {
        $orderEntity = $this->findOrCreateEntity($orderTransfer->getFkOrderReference());

        if ($orderEntity->isNew()) {
            $orderEntity->setFkOrderReference($orderTransfer->getFkOrderReference());
        }

        if ($orderTransfer->getFkGraphmastersTour() !== null) {
            $orderEntity->setFkGraphmastersTour($orderTransfer->getFkGraphmastersTour());
        }

        $orderEntity->setStatus($orderTransfer->getStatus());

        if($orderTransfer->getDeliveryOrder() !== null)
        {
            $orderEntity->setDeliveryOrder($orderTransfer->getDeliveryOrder());
        }

        if ($orderTransfer->getStopEta() !== null) {
            $orderEntity->setStopEta($this->toUtcDateTime($orderTransfer->getStopEta()));
        }

        if ($orderTransfer->getDeliveredAt() !== null) {
            $orderEntity->setDeliveredAt($this->toUtcDateTime($orderTransfer->getDeliveredAt()));
        }

        if ($orderEntity->isNew() || $orderEntity->isModified()) {
            $orderEntity->save();
        }

        return $orderEntity;
    }

    /**
     * @param DstGraphmastersOrder $orderEntity
     *
     * @return GraphMastersOrderTransfer
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function entityToTransfer(DstGraphmastersOrder $orderEntity): GraphMastersOrderTransfer
    {
        $orderTransfer = (new GraphMastersOrderTransfer())
            ->fromArray($orderEntity->toArray(), true);

        $orderTransfer
            ->setFkOrderReference($orderEntity->getFkOrderReference())
            ->setOrder($this->salesFacade->getOrderByReference($orderEntity->getFkOrderReference()))
            ->setStatus($orderEntity->getStatus());

        if ($orderEntity->getFkGraphmastersTour() !== null) {
            $orderTransfer->setFkGraphmastersTour($orderEntity->getFkGraphmastersTour());
            $orderTransfer->setTourReference($orderEntity->getDstGraphmastersTour()->getReference());
        }

        if ($orderEntity->getStopEta() !== null) {
            $orderTransfer->setStopEta($this->toLocalDateTimeString($orderEntity->getStopEta()));
        }

        if ($orderEntity->getDeliveredAt() !== null) {
            $orderTransfer->setDeliveredAt($this->toLocalDateTimeString($orderEntity->getDeliveredAt()));
        }

        return $orderTransfer;
    }

    /**
     * @param string $fkOrderReference
     *
     * @return GraphMastersOrderTransfer
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function getOrderByFkOrderReference(string $fkOrderReference): GraphMastersOrderTransfer
    {
        $orderEntity = $this
            ->queryContainer
            ->createGraphmastersOrderQuery()
            ->joinWithSpySalesOrder()
            ->findOneByFkOrderReference($fkOrderReference);

        if ($orderEntity === null) {
            throw EntityNotFoundException::reference($fkOrderReference);
        }

        return $this->entityToTransfer($orderEntity);
    }

    /**
     * @param array $fkOrderReferences
     *
     * @return GraphMastersOrderTransfer[]|array
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function getMultipleOrdersByFkOrderReferences(array $fkOrderReferences): array
    {
        $orderEntities = $this
            ->queryContainer
            ->createGraphmastersOrderQuery()
            ->joinWithSpySalesOrder()
            ->filterByFkOrderReference_In($fkOrderReferences)
            ->find();

        $orderTransfers = [];

        foreach ($orderEntities as $orderEntity) {
            $orderTransfers[] = $this->entityToTransfer($orderEntity);
        }

        return $orderTransfers;
    }

    /**
     * @param string $fkOrderReference
     *
     * @throws PropelException
     */
    public function delete(string $fkOrderReference): void
    {
        $orderEntity = $this
            ->queryContainer
            ->createGraphmastersOrderQuery()
            ->findOneByFkOrderReference($fkOrderReference);

        if ($orderEntity === null) {
            throw EntityNotFoundException::reference($fkOrderReference);
        }

        $orderEntity->delete();
    }

    /**
     * @param string $orderReference
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function markOrderFinishedByReference(string $orderReference): void
    {
        $this->markOrderStatusByReference(
            $orderReference,
            GraphMastersConstants::GRAPHMASTERS_ORDER_STATUS_FINISHED
        );
    }

    /**
     * @param string $orderReference
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function markOrderCancelledByReference(string $orderReference): void
    {
        $this->markOrderStatusByReference(
            $orderReference,
            GraphMastersConstants::GRAPHMASTERS_ORDER_STATUS_CANCELLED
        );
    }

    /**
     * @param string $orderReference
     *
     * @return bool
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function isOrderMarkedCancelled(string $orderReference): bool
    {
        $status = $this->getOrderByFkOrderReference($orderReference)->getStatus();

        return $status === GraphMastersConstants::GRAPHMASTERS_ORDER_STATUS_CANCELLED;
    }

    /**
     * @param string $fkOrderReference
     *
     * @return DstGraphmastersOrder
     */
    protected function findOrCreateEntity(string $fkOrderReference): DstGraphmastersOrder
    {
        $entity = $this
            ->queryContainer
            ->createGraphmastersOrderQuery()
            ->findOneByFkOrderReference($fkOrderReference);

        if ($entity === null) {
            return new DstGraphmastersOrder();
        }

        return $entity;
    }

    /**
     * @param string $orderReference
     * @param string $status
     *
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    protected function markOrderStatusByReference(string $orderReference, string $status): void
    {
        $graphmastersOrder = $this->getOrderByFkOrderReference($orderReference);

        $order = $graphmastersOrder->getOrder();

        $depotId = $this
            ->graphMastersSettingsModel
            ->getSettingsByIdBranch($order->getFkBranch())
            ->getDepotApiId();

        // @TODO: Is it really necessary to always set the date of delivery?
        $now = (new DateTime());

        $orderUpdateTransfer = (new GraphMastersApiOrderUpdateTransfer())
            ->setId($order->getOrderReference())
            ->setDepotId($depotId)
            ->setStatus($status)
            ->setGeoLocation(
                (new GraphMastersApiGeoLocationTransfer())
                    ->setLat($order->getShippingAddress()->getLat())
                    ->setLng($order->getShippingAddress()->getLng())
            )
            ->setDateOfDelivery($now->format(DATE_RFC3339));

        $this->orderHandler->importOrder($orderUpdateTransfer);

        $graphmastersOrder->setStatus($status);

        if ($status === GraphMastersConstants::GRAPHMASTERS_ORDER_STATUS_FINISHED) {
            $graphmastersOrder->setDeliveredAt($now->format(DATE_RFC3339));
        }

        $this->save($graphmastersOrder);
    }

    /**
     * @param string $dateTime
     *
     * @return DateTime
     */
    private function toUtcDateTime(string $dateTime): DateTime
    {
        return (new DateTime($dateTime, new DateTimeZone($this->config->getProjectTimeZone())))
            ->setTimezone(new DateTimeZone('UTC'));
    }

    /**
     * @param DateTime $dateTime
     *
     * @return string
     */
    private function toLocalDateTimeString(DateTime $dateTime): string
    {
        return $dateTime
            ->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()))
            ->format('Y-m-d H:i:s');
    }
}
