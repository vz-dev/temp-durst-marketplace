<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 19.10.18
 * Time: 13:24
 */

namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface ConcreteTourInterface
{
    /**
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @return ConcreteTourTransfer
     */
    public function createConcreteTourForConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer) : ConcreteTourTransfer;

    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return ConcreteTourTransfer
     */
    public function save(ConcreteTourTransfer $concreteTourTransfer) : ConcreteTourTransfer;

    /**
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function getConcreteTourById(int $idConcreteTour) : ConcreteTourTransfer;

    /**
     * @param array $idsConcreteTour
     * @return array
     * @throws AmbiguousComparisonException
     */
    public function getConcreteToursByIds(array $idsConcreteTour): array;

    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return ConcreteTourTransfer
     */
    public function comment(ConcreteTourTransfer $concreteTourTransfer) : ConcreteTourTransfer;

    /**
     * @return void
     */
    public function generateAllConcreteToursForExistingConcreteTimeSlotsInFuture() : void;

    /**
     * Set flag for a concrete tour that it can be exported now
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForExport(int $idConcreteTour): ConcreteTourTransfer;

    /**
     * Set flag for a concrete tour that it has been exported
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForBeingExported(int $idConcreteTour): ConcreteTourTransfer;

    /**
     * Set flag for a concrete tour to verify that it has been commissioned
     *
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     */
    public function flagConcreteTourForCommissioned(int $idConcreteTour): ConcreteTourTransfer;

    /**
     * Return a concrete tour by the given tour reference
     *
     * @param string $tourReference
     * @return ConcreteTourTransfer
     */
    public function getConcreteTourByTourReference(string $tourReference): ConcreteTourTransfer;

    /**
     * Return a list of concrete tours for a given driver
     *
     * @param DriverTransfer $driverTransfer
     * @return ConcreteTourTransfer[]
     */
    public function getConcreteToursByDriver(DriverTransfer $driverTransfer): array;

    /**
     * Checks, if a concrete tour by the given id is flag as commissioned
     *
     * @param int $idConcreteTour
     * @return bool
     */
    public function isConcreteTourCommissioned(int $idConcreteTour): bool;

    /**
     * Set status on concrete tour concerning deposit EDI export
     *
     * @param int $idConcreteTour
     * @param string $status
     * @return void
     */
    public function updateConcreteTourDepositEdiStatus(int $idConcreteTour, string $status): void;

    /**
     * Trigger the correct event from state machine to export the deposit EDI
     *
     * @param int $idConcreteTour
     * @return bool
     */
    public function triggerManualDepositEdiExport(int $idConcreteTour): bool;

    /**
     * Converts a concrete tour entity to a transfer object
     *
     * @param DstConcreteTour $concreteTourEntity
     * @return ConcreteTourTransfer
     */
    public function entityToTransfer(DstConcreteTour $concreteTourEntity): ConcreteTourTransfer;

    /**
     * Converts a concrete tour entity array to a transfer object hydrated for index only
     *
     * @param array $concreteTourEntityArray
     * @return ConcreteTourTransfer
     */
    public function entityArrayToTransferForIndex(array $concreteTourEntityArray): ConcreteTourTransfer;

    /**
     * Returns a list of the concrete tour dates for a given branch
     *
     * @param int $fkBranch
     * @param ConcreteTourTransfer[]
     * @return array
     * @throws AmbiguousComparisonException|PropelException
     */
    public function getConcreteTourDatesByFkBranch(int $fkBranch): array;
}
