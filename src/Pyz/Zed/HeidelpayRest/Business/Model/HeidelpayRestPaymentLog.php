<?php
/**
 * Durst - project - HeidelpayRestPaymentLog.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 21.01.20
 * Time: 11:38
 */

namespace Pyz\Zed\HeidelpayRest\Business\Model;


use Generated\Shared\Transfer\HeidelpayRestLogTransfer;
use Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLog;
use Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface;

class HeidelpayRestPaymentLog implements HeidelpayRestPaymentLogInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * HeidelpayRestPaymentLog constructor.
     * @param \Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface $queryContainer
     */
    public function __construct(
        HeidelpayRestQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param string $transactionType
     * @return \Generated\Shared\Transfer\HeidelpayRestLogTransfer|null
     */
    public function getHeidelpayRestLogByIdSalesOrderAndTransactionType(
        int $idSalesOrder,
        string $transactionType
    ): ?HeidelpayRestLogTransfer
    {
        $heidelpayLogEntity = $this
            ->queryContainer
            ->queryLogByIdSalesOrderAndType(
                $idSalesOrder,
                $transactionType
            )
            ->findOne();

        if ($heidelpayLogEntity === null || $heidelpayLogEntity->getIdPaymentHeidelpayRestLog() === null) {
            return null;
        }

        return $this
            ->entityToTransfer($heidelpayLogEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return HeidelpayRestLogTransfer[]
     */
    public function getHeidelpayRestLogsByIdSalesOrder(
        int $idSalesOrder
    ): array
    {
        $heidelpayLogEntities = $this
            ->queryContainer
            ->queryLogByIdSalesOrder(
                $idSalesOrder
            )
            ->orderByCreatedAt()
            ->find();

        $heidelpayLogTransfers = [];

        foreach ($heidelpayLogEntities as $heidelpayLogEntity) {
            $heidelpayLogTransfers[] = $this
                ->entityToTransfer(
                    $heidelpayLogEntity
                );
        }

        return $heidelpayLogTransfers;
    }

    /**
     * @param \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLog $dstPaymentHeidelpayRestLog
     * @return \Generated\Shared\Transfer\HeidelpayRestLogTransfer
     */
    protected function entityToTransfer(DstPaymentHeidelpayRestLog $dstPaymentHeidelpayRestLog): HeidelpayRestLogTransfer
    {
        $transfer = (new HeidelpayRestLogTransfer())
            ->fromArray(
                $dstPaymentHeidelpayRestLog->toArray(),
                true
            );

        return $transfer;
    }
}
