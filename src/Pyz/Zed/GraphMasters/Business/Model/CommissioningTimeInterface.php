<?php

namespace Pyz\Zed\GraphMasters\Business\Model;

use Generated\Shared\Transfer\GraphMastersCommissioningTimeTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersCommissioningTime;
use Propel\Runtime\Exception\PropelException;

interface CommissioningTimeInterface
{
    /**
     * @param GraphMastersCommissioningTimeTransfer $transfer
     *
     * @return DstGraphmastersCommissioningTime
     *
     * @throws PropelException
     */
    public function save(GraphMastersCommissioningTimeTransfer $transfer): DstGraphmastersCommissioningTime;

    /**
     * @param int $idCommissioningTime
     *
     * @return void
     *
     * @throws PropelException
     */
    public function remove(int $idCommissioningTime): void;

    /**
     * @param DstGraphmastersCommissioningTime $entity
     *
     * @return GraphMastersCommissioningTimeTransfer
     */
    public function entityToTransfer(DstGraphmastersCommissioningTime $entity): GraphMastersCommissioningTimeTransfer;
}
