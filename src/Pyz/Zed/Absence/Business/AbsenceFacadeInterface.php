<?php
/**
     * Created by PhpStorm.
     * User: Giuliano
     * Date: 25.01.18
     * Time: 16:16
     */

namespace Pyz\Zed\Absence\Business;


use DateTime;
use Generated\Shared\Transfer\AbsenceTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface AbsenceFacadeInterface
{
    /**
     * Returnes an array of absence transfer objects.
     *
     * @return AbsenceTransfer[]
     * @throws ContainerKeyNotFoundException
     * @throws AmbiguousComparisonException
     */
    public function getAbsencesForCurrentBranch();

    /**
     * Returns an array future absence transfer objects for the branch with the given branch id.
     *
     * @param int $idBranch
     * @return AbsenceTransfer[]
     */
    public function getAbsencesForBranchByIdBranch(int $idBranch): array;

    /**
     * Removes the absence object defined by the given id from the database.
     *
     * @param int $idAbsence
     * @return void
     * @throws Exception\AbsenceNotFoundException if no absence object with the given id can
     * be found in the database
     * @throws PropelException
     * @throws ContainerKeyNotFoundException
     * @throws AmbiguousComparisonException
     */
    public function removeAbsenceById($idAbsence);

    /**
     * Persists the data of the given absence transfer object to the database
     * and returns a fully hydrated object representing the persisted data.
     *
     * @param AbsenceTransfer $absenceTransfer
     * @return AbsenceTransfer
     */
    public function addAbsence(AbsenceTransfer $absenceTransfer);

    /**
     * Checks whether a given branch defined by its id is available in the given time frame
     * defined by start and end time.
     *
     * @param int $idBranch
     * @param DateTime $start
     * @param DateTime $end
     * @return bool
     */
    public function isBranchAbsent(int $idBranch, DateTime $start, DateTime $end) : bool;
}
