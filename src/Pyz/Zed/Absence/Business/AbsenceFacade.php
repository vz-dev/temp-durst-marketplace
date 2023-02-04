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
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class AbsenceFacade
 * @package Pyz\Zed\Absence\Business
 * @method AbsenceBusinessFactory getFactory()
 */
class AbsenceFacade extends AbstractFacade implements AbsenceFacadeInterface
{
    /**
     * @return AbsenceTransfer[]
     */
    public function getAbsencesForCurrentBranch()
    {
        return $this
            ->getFactory()
            ->createAbsenceModel()
            ->getAbsencesForCurrentBranch();
    }

    /**
     * @param int $idBranch
     *
     * @return array
     */
    public function getAbsencesForBranchByIdBranch(int $idBranch) : array
    {
        return $this
            ->getFactory()
            ->createAbsenceModel()
            ->getAbsencesForBranchByIdBranch($idBranch);
    }

    /**
     * Removes the absence object defined by the given id from the database.
     *
     * @param int $idAbsence
     *
     * @return void
     */
    public function removeAbsenceById($idAbsence)
    {
        $this
            ->getFactory()
            ->createAbsenceModel()
            ->removeAbsenceById($idAbsence);
    }

    /**
     * {@inheritdoc}
     *
     * @param AbsenceTransfer $absenceTransfer
     * @return AbsenceTransfer
     */
    public function addAbsence(AbsenceTransfer $absenceTransfer)
    {
        return $this
            ->getFactory()
            ->createAbsenceModel()
            ->addAbsence($absenceTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @param DateTime $start
     * @param DateTime $end
     * @return bool
     */
    public function isBranchAbsent(int $idBranch, DateTime $start, DateTime $end): bool
    {
        return $this
            ->getFactory()
            ->createAbsenceModel()
            ->isBranchAbsent($idBranch, $start, $end);
    }
}
