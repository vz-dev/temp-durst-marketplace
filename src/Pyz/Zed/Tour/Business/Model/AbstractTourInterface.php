<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 02.10.18
 * Time: 14:37
 */

namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\AbstractTourTransfer;
use Orm\Zed\Tour\Persistence\DstAbstractTour;

interface AbstractTourInterface
{
    /**
     * @param AbstractTourTransfer $abstractTourTransfer
     * @return AbstractTourTransfer
     */
    public function save(AbstractTourTransfer $abstractTourTransfer) : AbstractTourTransfer;

    /**
     * @param int $idBranch
     * @return AbstractTourTransfer[]
     */
    public function getabstractToursByFkBranch(int $idBranch) : array;

    /**
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function getAbstractTourById(int $idAbstractTour) : AbstractTourTransfer;

    /**
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function activate(int $idAbstractTour) : AbstractTourTransfer;

    /**
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function deactivate(int $idAbstractTour) : AbstractTourTransfer;

    /**
     * @param int $idAbstractTour
     * @return AbstractTourTransfer
     */
    public function delete(int $idAbstractTour) : AbstractTourTransfer;

    /**
     * @param int $idAbstractTour
     * @return bool
     */
    public function hasPreparationStartingSameTimeForAllTimeSlots(int $idAbstractTour) : bool;

    /**
     * @param DstAbstractTour $abstractTourEntity
     * @return AbstractTourTransfer
     */
    public function entityToTransfer(DstAbstractTour $abstractTourEntity): AbstractTourTransfer;
}
