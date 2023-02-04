<?php


namespace Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator;


use Generated\Shared\Transfer\ConcreteTourTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Pyz\Zed\Driver\Business\DriverFacadeInterface;

class DriverConcreteTourHydrator implements ConcreteTourHydratorInterface
{
    /**
     * @var \Pyz\Zed\Driver\Business\DriverFacadeInterface
     */
    protected $driverFacade;

    /**
     * DriverConcreteTourHydrator constructor.
     * @param \Pyz\Zed\Driver\Business\DriverFacadeInterface $driverFacade
     */
    public function __construct(DriverFacadeInterface $driverFacade)
    {
        $this->driverFacade = $driverFacade;
    }

    /**
     * @param DstConcreteTour $concreteTourEntity
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return void
     */
    public function hydrateConcreteTour(DstConcreteTour $concreteTourEntity, ConcreteTourTransfer $concreteTourTransfer): void
    {
        if ($concreteTourEntity->getFkDriver() !== null) {
            $driver = $this
                ->driverFacade
                ->convertDriverEntityToTransfer($concreteTourEntity->getDstDriver());

            $concreteTourTransfer
                ->setDriver($driver);
        }
    }
}
