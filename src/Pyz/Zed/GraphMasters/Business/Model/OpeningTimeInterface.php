<?php

namespace Pyz\Zed\GraphMasters\Business\Model;

use Generated\Shared\Transfer\GraphMastersOpeningTimeTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOpeningTime;
use Propel\Runtime\Exception\PropelException;

interface OpeningTimeInterface
{
    /**
     * @param GraphMastersOpeningTimeTransfer $transfer
     *
     * @return DstGraphmastersOpeningTime
     *
     * @throws PropelException
     */
    public function save(GraphMastersOpeningTimeTransfer $transfer): DstGraphmastersOpeningTime;

    /**
     * @param int $idOpeningTime
     *
     * @return void
     *
     * @throws PropelException
     */
    public function remove(int $idOpeningTime): void;

    /**
     * @param DstGraphmastersOpeningTime $entity
     *
     * @return GraphMastersOpeningTimeTransfer
     */
    public function entityToTransfer(DstGraphmastersOpeningTime $entity): GraphMastersOpeningTimeTransfer;
}
