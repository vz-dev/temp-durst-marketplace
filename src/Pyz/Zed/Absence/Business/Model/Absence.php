<?php
/**
     * Created by PhpStorm.
     * User: Giuliano
     * Date: 25.01.18
     * Time: 16:18
     */

namespace Pyz\Zed\Absence\Business\Model;

use DateTime;
use Generated\Shared\Transfer\AbsenceTransfer;
use Orm\Zed\Absence\Persistence\Map\SpyAbsenceTableMap;
use Orm\Zed\Absence\Persistence\SpyAbsence;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Absence\Business\Exception\AbsenceNotFoundException;
use Pyz\Zed\Absence\Business\Exception\InvalidBranchException;
use Pyz\Zed\Absence\Business\Exception\StartAfterEndException;
use Pyz\Zed\Absence\Persistence\AbsenceQueryContainerInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class Absence
{
    const DATE_FORMAT = 'd.m.Y';

    const CONDITION_START_GE_START_DATE = 'CONDITION_START_GE_START_DATE';
    const CONDITION_START_LE_END_DATE = 'CONDITION_START_LE_END_DATE';
    const CONDITION_START_BETWEEN_START_DATE_AND_END_DATE = 'CONDITION_START_BETWEEN_START_DATE_AND_END_DATE';
    const CONDITION_END_GE_START_DATE = 'CONDITION_END_GE_START_DATE';
    const CONDITION_END_LE_END_DATE = 'CONDITION_END_LE_END_DATE';
    const CONDITION_END_BETWEEN_START_DATE_AND_END_DATE = 'CONDITION_END_BETWEEN_START_DATE_AND_END_DATE';
    const CONDITION_START_LE_START_DATE = 'CONDITION_START_LE_START_DATE';
    const CONDITION_END_GE_END_DATE = 'CONDITION_END_GE_END_DATE';
    const CONDITION_ABSENCE_BETWEEN_START_AND_END = 'CONDITION_ABSENCE_BETWEEN_START_AND_END';


    /**
     * @var AbsenceQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;


    /**
     * Absence constructor.
     * @param AbsenceQueryContainerInterface $queryContainer
     * @param MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        AbsenceQueryContainerInterface $queryContainer,
        MerchantFacadeInterface $merchantFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->merchantFacade = $merchantFacade;
    }


    /**
     * @return array
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function getAbsencesForCurrentBranch()
    {
        return $this
            ->getAbsencesForBranchByIdBranch(
                $this->merchantFacade->getCurrentBranch()->getIdBranch()
            );
    }

    /**
     * @param int $idBranch
     * @return array
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function getAbsencesForBranchByIdBranch(int $idBranch)
    {
        $entities = $this
            ->queryContainer
            ->queryAbsence()
            ->filterByFkBranch($idBranch)
            ->filterByEndDate('now', '>=')
            ->orderByStartDate()
            ->find();

        $transfers = [];
        foreach($entities as $entity){
            $transfers[] = $this
                ->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * @param $idAbsence
     * @throws AbsenceNotFoundException
     * @throws PropelException
     * @throws AmbiguousComparisonException
     * @throws InvalidBranchException
     */
    public function removeAbsenceById($idAbsence)
    {
        $entity = $this
            ->queryContainer
            ->queryAbsence()
            ->filterByIdAbsence($idAbsence)
            ->findOne();

        if($entity === null) {
            throw new AbsenceNotFoundException(
                sprintf(
                    AbsenceNotFoundException::NOT_FOUND,
                    $idAbsence
                )
            );
        }

        if($entity->getFkBranch() !== $this->merchantFacade->getCurrentBranch()->getIdBranch()){
            throw new InvalidBranchException(
                InvalidBranchException::MESSAGE
            );
        }

        $entity->delete();
    }

    /**
     * @param int $idBranch
     * @param DateTime $start
     * @param DateTime $end
     * @return bool
     * @throws AmbiguousComparisonException
     */
    public function isBranchAbsent(int $idBranch, DateTime $start, DateTime $end) : bool
    {
         return $this
            ->queryContainer
            ->queryAbsence()
            ->filterByFkBranch($idBranch)
            ->condition(
                self::CONDITION_START_GE_START_DATE,
                SpyAbsenceTableMap::COL_START_DATE . '<= ?',
                $start
            )
            ->condition(
                self::CONDITION_START_LE_END_DATE,
                SpyAbsenceTableMap::COL_END_DATE . '>= ?',
                $start
            )
            ->combine(
                [self::CONDITION_START_LE_END_DATE, self::CONDITION_START_GE_START_DATE],
                'and',
                self::CONDITION_START_BETWEEN_START_DATE_AND_END_DATE
            )
            ->condition(
             self::CONDITION_END_GE_START_DATE,
             SpyAbsenceTableMap::COL_START_DATE . '<= ?',
             $end
            )
            ->condition(
             self::CONDITION_END_LE_END_DATE,
             SpyAbsenceTableMap::COL_END_DATE . '>= ?',
             $end
            )
            ->combine(
                 [self::CONDITION_END_LE_END_DATE, self::CONDITION_END_GE_START_DATE],
                 'and',
                 self::CONDITION_END_BETWEEN_START_DATE_AND_END_DATE
            )
             ->condition(
                 self::CONDITION_START_LE_START_DATE,
                 SpyAbsenceTableMap::COL_START_DATE . '>= ?',
                 $start
             )
             ->condition(
                 self::CONDITION_END_GE_END_DATE,
                 SpyAbsenceTableMap::COL_END_DATE . '<= ?',
                 $end
             )
             ->combine(
                 [self::CONDITION_START_LE_START_DATE, self::CONDITION_END_GE_END_DATE],
                 'and',
                 self::CONDITION_ABSENCE_BETWEEN_START_AND_END
             )
            ->where(
                [
                    self::CONDITION_START_BETWEEN_START_DATE_AND_END_DATE,
                    self::CONDITION_END_BETWEEN_START_DATE_AND_END_DATE,
                    self::CONDITION_ABSENCE_BETWEEN_START_AND_END
                ],
                'or'
            )->count() !== 0;
    }

    /**
     * @param AbsenceTransfer $absenceTransfer
     * @return AbsenceTransfer
     * @throws PropelException
     * @throws StartAfterEndException
     */
    public function addAbsence(AbsenceTransfer $absenceTransfer)
    {
        $absence = new SpyAbsence();

        $absenceTransfer->requireStartDate()->requireEndDate();

        $start = $this->getDateFromString($absenceTransfer->getStartDate());
        $end = $this->getDateFromString($absenceTransfer->getEndDate());

        if($start > $end){
            throw new StartAfterEndException(
                sprintf(
                    StartAfterEndException::MESSAGE,
                    $absenceTransfer->getStartDate(),
                    $absenceTransfer->getEndDate()
                )
            );
        }

        $absence->setStartDate($start);
        $absence->setEndDate($end);
        $absence->setDescription($absenceTransfer->getDescription());
        $absence->setFkBranch($this->merchantFacade->getCurrentBranch()->getIdBranch());
        $absence->save();

        return $this->entityToTransfer($absence);
    }

    /**
     * @param string $dateString
     * @return DateTime
     */
    protected function getDateFromString(string $dateString) : DateTime
    {
        return DateTime::createFromFormat(self::DATE_FORMAT, $dateString);
    }

    /**
     * @param SpyAbsence $absenceEntity
     * @return AbsenceTransfer
     * @throws PropelException
     */
    protected function entityToTransfer(SpyAbsence $absenceEntity)
    {
        $transfer = new AbsenceTransfer();
        $transfer->fromArray($absenceEntity->toArray(), true);
        $transfer->setStartDate($absenceEntity->getStartDate()->format(self::DATE_FORMAT));
        $transfer->setEndDate($absenceEntity->getEndDate()->format(self::DATE_FORMAT));

        return $transfer;
    }
}
