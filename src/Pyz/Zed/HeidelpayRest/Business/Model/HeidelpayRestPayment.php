<?php
/**
 * Durst - project - HeidelpayRest.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.01.19
 * Time: 14:59
 */

namespace Pyz\Zed\HeidelpayRest\Business\Model;

use Generated\Shared\Transfer\HeidelpayRestPaymentTransfer;
use Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRest;
use Pyz\Zed\HeidelpayRest\Business\Exception\InvalidArgumentException;
use Pyz\Zed\HeidelpayRest\Business\Exception\PaymentNotFoundException;
use Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface;

class HeidelpayRestPayment implements HeidelpayRestPaymentInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * HeidelpayRestPayment constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface $queryContainer
     */
    public function __construct(HeidelpayRestQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idSalesOrder
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\PaymentNotFoundException
     * @return \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer
     */
    public function getHeidelpayRestPaymentByIdSalesOrder(int $idSalesOrder): HeidelpayRestPaymentTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryPaymentByIdSalesOrder($idSalesOrder)
            ->findOne();

        if ($entity === null) {
            throw new PaymentNotFoundException(
                sprintf(
                    PaymentNotFoundException::MESSAGE,
                    $idSalesOrder
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $orderRef
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\PaymentNotFoundException
     * @return \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer
     */
    public function getHeidelpayRestPaymentBySalesOrderRef(string $orderRef): HeidelpayRestPaymentTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryPaymentBySalesOrderRef($orderRef)
            ->findOne();

        if ($entity === null) {
            throw new PaymentNotFoundException(
                sprintf(
                    PaymentNotFoundException::MESSAGE_ORDER_REF,
                    $orderRef
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $paymentId
     *
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\PaymentNotFoundException
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer
     */
    public function getHeidelpayRestPaymentByPaymentId(string $paymentId): HeidelpayRestPaymentTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryPaymentByPaymentId($paymentId)
            ->findOne();

        if ($entity === null) {
            throw new PaymentNotFoundException(
                sprintf(
                    PaymentNotFoundException::MESSAGE_PAYMENT_ID,
                    $paymentId
                )
            );
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $heidelpayRestPaymentTransfer
     *
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\InvalidArgumentException
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer
     */
    public function addHeidelpayRestPayment(HeidelpayRestPaymentTransfer $heidelpayRestPaymentTransfer): HeidelpayRestPaymentTransfer
    {
        if ($heidelpayRestPaymentTransfer->getIdPaymentRest() !== null) {
            throw new InvalidArgumentException(
                sprintf(
                    InvalidArgumentException::PAYMENT_EXISTS,
                    $heidelpayRestPaymentTransfer->getIdPaymentRest()
                )
            );
        }

        $heidelpayRestPaymentTransfer
            ->requirePaymentTypeId()
            ->requireFkSalesOrder();

        $entity = $this
            ->hydrateEntityFromTransfer(new DstPaymentHeidelpayRest(), $heidelpayRestPaymentTransfer);

        $entity->save();

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $heidelpayRestPaymentTransfer
     * @throws \Pyz\Zed\HeidelpayRest\Business\Exception\InvalidArgumentException
     */
    public function updateHeidelpayRestPayment(HeidelpayRestPaymentTransfer $heidelpayRestPaymentTransfer): void
    {
        $heidelpayRestPaymentTransfer->requireIdPaymentRest();

        $entity = $this
            ->queryContainer
            ->queryPaymentByIdPaymentHeidelpayRest($heidelpayRestPaymentTransfer->getIdPaymentRest())
            ->findOne();

        if ($entity === null) {
            throw new InvalidArgumentException(
                sprintf(
                    InvalidArgumentException::MESSAGE,
                    $heidelpayRestPaymentTransfer->getIdPaymentRest()
                )
            );
        }

        $entity = $this
            ->hydrateEntityFromTransfer($entity, $heidelpayRestPaymentTransfer);

        if ($entity->isModified() === true) {
            $entity->save();
        }
    }

    /**
     * @param \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRest $entity
     * @param \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer $transfer
     *
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRest
     */
    protected function hydrateEntityFromTransfer(
        DstPaymentHeidelpayRest $entity,
        HeidelpayRestPaymentTransfer $transfer
    ): DstPaymentHeidelpayRest {
        $entity
            ->fromArray($transfer->toArray());

        return $entity;
    }

    /**
     * @param \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRest $entity
     *
     * @return \Generated\Shared\Transfer\HeidelpayRestPaymentTransfer
     */
    protected function entityToTransfer(DstPaymentHeidelpayRest $entity): HeidelpayRestPaymentTransfer
    {
        $transfer = (new HeidelpayRestPaymentTransfer())
            ->fromArray($entity->toArray(), true);

        $transfer
            ->setIdPaymentRest($entity->getIdPaymentHeidelpayRest());

        return $transfer;
    }
}
