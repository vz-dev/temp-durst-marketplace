<?php


namespace Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator;


use ArrayObject;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Pyz\Zed\Driver\Business\DriverFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

/**
 * Class AvailableDriverConcreteTourHydrator
 * @package Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator
 * @method TourFacadeInterface getFacade()
 */
class AvailableDriverConcreteTourHydrator implements ConcreteTourHydratorInterface
{
    /**
     * @var \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\Driver\Business\DriverFacadeInterface
     */
    protected $driverFacade;

    /**
     * AvailableDriverConcreteTourHydrator constructor.
     * @param \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\Driver\Business\DriverFacadeInterface $driverFacade
     */
    public function __construct(
        TourQueryContainerInterface $queryContainer,
        DriverFacadeInterface $driverFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->driverFacade = $driverFacade;
    }

    /**
     * @param \Orm\Zed\Tour\Persistence\DstConcreteTour $concreteTourEntity
     * @param \Generated\Shared\Transfer\ConcreteTourTransfer $concreteTourTransfer
     * @throws \Exception
     */
    public function hydrateConcreteTour(DstConcreteTour $concreteTourEntity, ConcreteTourTransfer $concreteTourTransfer)
    {
        $availableDrivers = $this
            ->findAvailableDriversForConcreteTour($concreteTourTransfer);

        $concreteTourTransfer
            ->setAvailableDrivers(new ArrayObject($availableDrivers));
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\ConcreteTourTransfer $concreteTourTransfer
     * @return \Generated\Shared\Transfer\DriverTransfer[]
     * @throws \Exception
     */
    public function findAvailableDriversForConcreteTour(ConcreteTourTransfer $concreteTourTransfer): array
    {
        return $this
            ->driverFacade
            ->getDriversFromBranchWithExcludedDrivers(
                $concreteTourTransfer->getFkBranch(),
                []
            );
    }
}
