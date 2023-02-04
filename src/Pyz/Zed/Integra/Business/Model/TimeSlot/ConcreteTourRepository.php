<?php
/**
 * Durst - project - ConcreteTourRepository.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.11.20
 * Time: 16:05
 */

namespace Pyz\Zed\Integra\Business\Model\TimeSlot;

use Orm\Zed\Tour\Persistence\DstAbstractTour;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Orm\Zed\Tour\Persistence\Map\DstAbstractTourTableMap;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use Pyz\Shared\Integra\IntegraConstants;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;

class ConcreteTourRepository implements ConcreteTourRepositoryInterface
{
    /**
     * @var IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var array[] mapping of references to ids
     */
    protected $tourIds;


    /**
     * @var array[] mapping of references to dates
     */
    protected $refsToDates;

    /**
     * @var int
     */
    protected $abstractTourId = -1;

    /**
     * ConcreteTourRepository constructor.
     *
     * @param IntegraQueryContainerInterface $queryContainer
     */
    public function __construct(IntegraQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
        $this->tourIds = [];
        $this->refsToDates = [];
    }

    /**
     * @param string $tourReference
     *
     * @return int
     */
    public function getTourIdByReference(string $tourReference): int
    {
        return $this->tourIds[$tourReference];
    }

    /**
     * @param string $tourReference
     * @param int $idBranch
     *
     * @return int
     */
    protected function addTour(string $tourReference, int $idBranch): int
    {
        $entity = (new DstConcreteTour())
            ->setFkBranch($idBranch)
            ->setTourReference($tourReference)
            ->setDate($this->refsToDates[$tourReference])
            ->setFkAbstractTour($this->getAbstractTourId($idBranch));

        $entity->save();

        return $entity->getIdConcreteTour();
    }

    /**
     * @param int $idBranch
     *
     * @return int
     */
    protected function getAbstractTourId(int $idBranch): int
    {
        if ($this->abstractTourId === -1) {
            $tourId = $this
                ->queryContainer
                ->queryIntegraTour($idBranch)
                ->findOne();

            $this->abstractTourId = $tourId;
            if ($tourId === null) {
                $this->abstractTourId = $this->createIntegraTour($idBranch);
            }
        }

        return $this->abstractTourId;
    }

    /**
     * @param int $idBranch
     *
     * @return int
     */
    protected function createIntegraTour(int $idBranch): int
    {
        $entity = (new DstAbstractTour())
            ->setName(IntegraConstants::INTEGRA_TOUR_NAME)
            ->setFkBranch($idBranch)
            ->setStatus(DstAbstractTourTableMap::COL_STATUS_DEACTIVATED);

        $entity->save();

        return $entity->getIdAbstractTour();
    }

    /**
     * @param array $references
     * @param int $idBranch
     *
     * @return int
     */
    public function loadTours(array $references, int $idBranch, array $refsToDates): int
    {
        $this->refsToDates = $refsToDates;

        $tours = $this
            ->queryContainer
            ->queryConcreteToursByReferencesForBranch($references, $idBranch)
            ->find()
            ->toArray();

        foreach ($tours as $tour) {
            $this->tourIds[$tour[DstConcreteTourTableMap::COL_TOUR_REFERENCE]] = $tour[DstConcreteTourTableMap::COL_ID_CONCRETE_TOUR];
        }

        $addedTours = 0;
        foreach ($references as $reference) {
            if (array_key_exists($reference, $this->tourIds) !== true) {
                $this->tourIds[$reference] = $this->addTour($reference, $idBranch);
                $addedTours++;
            }
        }

        return $addedTours;
    }
}
