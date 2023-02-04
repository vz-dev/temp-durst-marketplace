<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-12-12
 * Time: 15:15
 */

namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\ConcreteTourExportTransfer;

interface ConcreteTourExportInterface
{
    /**
     * @param ConcreteTourExportTransfer $concreteTourExportTransfer
     * @return ConcreteTourExportTransfer
     */
    public function save(ConcreteTourExportTransfer $concreteTourExportTransfer): ConcreteTourExportTransfer;

    /**
     * @param int $idConcreteTourExport
     * @return void
     */
    public function removeById(int $idConcreteTourExport);

    /**
     * @return ConcreteTourExportTransfer[]
     */
    public function getConcreteToursForEdiExport(): array;

    /**
     * @return int
     */
    public function saveConcreteToursToExport(): int;

    /**
     * @param int $idConcreteTourExport
     */
    public function setExportInProgress(int $idConcreteTourExport): void;

    /**
     * @param int $idConcreteTour
     * @return \Generated\Shared\Transfer\ConcreteTourExportTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\Tour\Business\Exception\EntityNotFoundExceptions
     */
    public function getConcreteTourExportByIdConcreteTour(int $idConcreteTour): ConcreteTourExportTransfer;
}
