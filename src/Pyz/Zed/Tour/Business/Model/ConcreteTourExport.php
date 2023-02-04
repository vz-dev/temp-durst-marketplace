<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-12-12
 * Time: 15:15
 */

namespace Pyz\Zed\Tour\Business\Model;


use DateTime;
use Generated\Shared\Transfer\ConcreteTourExportTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\SpyBranchEntityTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Orm\Zed\Tour\Persistence\DstConcreteTourExport;
use PDO;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Tour\Business\Exception\EntityNotFoundException;
use Pyz\Zed\Tour\Business\Exception\TourExportException;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Pyz\Zed\Tour\TourConfig;

class ConcreteTourExport implements ConcreteTourExportInterface
{
    protected const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var TourConfig
     */
    protected $tourConfig;

    /**
     * ConcreteTourExport constructor.
     * @param TourQueryContainerInterface $queryContainer
     * @param TourConfig $tourConfig
     */
    public function __construct(TourQueryContainerInterface $queryContainer, TourConfig $tourConfig)
    {
        $this->queryContainer = $queryContainer;
        $this->tourConfig = $tourConfig;
    }

    /**
     * {@inheritdoc}
     *
     * @param ConcreteTourExportTransfer $concreteTourExportTransfer
     * @return ConcreteTourExportTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function save(ConcreteTourExportTransfer $concreteTourExportTransfer): ConcreteTourExportTransfer
    {
        $concreteTourExportEntity = $this
            ->findEntityOrCreate($concreteTourExportTransfer);

        $concreteTourExportEntity
            ->setFkConcreteTour($concreteTourExportTransfer->getConcreteTour()->getIdConcreteTour())
            ->setFkBranch($concreteTourExportTransfer->getBranch()->getIdBranch())
            ->setCreatedAt($concreteTourExportTransfer->getCreatedAt())
            ->setIdConcreteTourExport($concreteTourExportTransfer->getIdConcreteTourExport())
            ->setInProgress($concreteTourExportTransfer->getInProgress());

        if ($concreteTourExportEntity->isNew() || $concreteTourExportEntity->isModified()) {
            $concreteTourExportEntity->save();
        }

        return $this
            ->createTransferFromEntity($concreteTourExportEntity);
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idConcreteTourExport
     * @throws TourExportException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function removeById(int $idConcreteTourExport)
    {
        $concreteTourEntity = $this
            ->queryContainer
            ->queryConcreteTourExportById($idConcreteTourExport)
            ->findOne();

        if ($concreteTourEntity === null) {
            throw new TourExportException(sprintf(
                TourExportException::ERROR_CONCRETE_TOUR_EXPORT_NOT_FOUND,
                $idConcreteTourExport
            ));
        }

        $concreteTourEntity->delete();
    }

    /**
     * {@inheritdoc}
     *
     * @return ConcreteTourExportTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getConcreteToursForEdiExport(): array
    {
        $concreteTourExports = $this
            ->queryContainer
            ->queryConcreteTourExport()
            ->filterByInProgress(false)
            ->orderByCreatedAt(Criteria::ASC)
            ->find();

        $concreteTourExportTransfers = [];

        foreach ($concreteTourExports as $concreteTourExport) {
            $concreteTourExportTransfers[] = $this
                ->createTransferFromEntity($concreteTourExport);
        }

        return $concreteTourExportTransfers;
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     * @throws \Exception
     */
    public function saveConcreteToursToExport(): int
    {
        $currentDate = new DateTime('now');

        $concreteToursQuery = $this
            ->queryContainer
            ->queryConcreteTour()
            ->joinWith('SpyConcreteTimeSlot scts')
            ->joinWith('scts.SpyTimeSlot sts')
            ->joinWith('SpyBranch b')
            ->useDstConcreteTourExportQuery()
                ->filterByIdConcreteTourExport(null,  Criteria::ISNULL)
            ->endUse()
            ->where('(scts.StartTime - make_interval(0,0,0,0,0,sts.PrepTime)) <= ?', $currentDate->format(self::DATETIME_FORMAT), PDO::PARAM_STR)
            ->filterByExportable(true)
            ->filterByFkStateMachineItemState(null, Criteria::ISNULL)
            ->filterByIsCommissioned(false);

        $concreteTours = $concreteToursQuery
            ->find();

        $concreteTourExportTransfers = [];

        /* @var $concreteTour DstConcreteTour */
        foreach ($concreteTours as $concreteTour) {
            $branch = $concreteTour
                ->getSpyBranch();
            $branchTransfer = (new SpyBranchEntityTransfer())
                ->fromArray($branch->toArray(), true);

            $concreteTourTransfer = (new ConcreteTourTransfer())
                ->fromArray($concreteTour->toArray(), true);

            $concreteTourExportTransfer = (new ConcreteTourExportTransfer())
                ->setConcreteTour($concreteTourTransfer)
                ->setBranch($branchTransfer)
                ->setCreatedAt($currentDate->format(self::DATETIME_FORMAT))
                ->setInProgress(false);

            $concreteTourExportTransfers[] = $this
                ->save($concreteTourExportTransfer);
        }

        return count($concreteTourExportTransfers);
    }

    /**
     * @param DstConcreteTourExport $dstConcreteTourExport
     * @return ConcreteTourExportTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createTransferFromEntity(DstConcreteTourExport $dstConcreteTourExport): ConcreteTourExportTransfer
    {
        $concreteTourExportTransfer = new ConcreteTourExportTransfer();

        $concreteTour = $dstConcreteTourExport
            ->getDstConcreteTour();
        $concreteTourTransfer = (new ConcreteTourTransfer())
            ->fromArray($concreteTour->toArray(), true);

        $branch = $dstConcreteTourExport
            ->getSpyBranch();
        $branchTransfer = (new SpyBranchEntityTransfer())
            ->fromArray($branch->toArray(), true);

        $concreteTourExportTransfer
            ->setBranch($branchTransfer)
            ->setConcreteTour($concreteTourTransfer)
            ->setCreatedAt($dstConcreteTourExport->getCreatedAt(self::DATETIME_FORMAT))
            ->setIdConcreteTourExport($dstConcreteTourExport->getIdConcreteTourExport())
            ->setInProgress($dstConcreteTourExport->getInProgress());

        return $concreteTourExportTransfer;
    }

    /**
     * @param ConcreteTourExportTransfer $concreteTourExportTransfer
     * @return DstConcreteTourExport
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function findEntityOrCreate(ConcreteTourExportTransfer $concreteTourExportTransfer): DstConcreteTourExport
    {
        if ($concreteTourExportTransfer->getIdConcreteTourExport() === null) {
            return new DstConcreteTourExport();
        }

        return $this
            ->queryContainer
            ->queryConcreteTourExportById($concreteTourExportTransfer->getIdConcreteTourExport())
            ->findOneOrCreate();
    }

    /**
     * @param int $idConcreteTourExport
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function setExportInProgress(int $idConcreteTourExport): void
    {
        $concreteTourExport = $this
            ->queryContainer
            ->queryConcreteTourExportById($idConcreteTourExport)
            ->findOne();

        if ($concreteTourExport->getIdConcreteTourExport() !== null) {
            $concreteTourExport
                ->setInProgress(true);

            $exportTransfer = $this
                ->createTransferFromEntity($concreteTourExport);

            $this
                ->save($exportTransfer);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return \Generated\Shared\Transfer\ConcreteTourExportTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getConcreteTourExportByIdConcreteTour(int $idConcreteTour): ConcreteTourExportTransfer
    {
        $concreteTourExportEntity = $this
            ->queryContainer
            ->queryConcreteTourExport()
            ->filterByFkConcreteTour($idConcreteTour)
            ->findOne();

        if($concreteTourExportEntity === null){
            throw new EntityNotFoundException($idConcreteTour);
        }

        return $this
            ->createTransferFromEntity($concreteTourExportEntity);
    }
}
