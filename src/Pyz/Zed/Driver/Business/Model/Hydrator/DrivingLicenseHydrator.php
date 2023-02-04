<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 27.08.18
 * Time: 13:53
 */

namespace Pyz\Zed\Driver\Business\Model\Hydrator;

use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Driver\Persistence\DstDriver;
use Pyz\Zed\Tour\Business\TourFacadeInterface;

class DrivingLicenseHydrator implements DrivingLicenseHydratorInterface
{
    /**
     * @var \Pyz\Zed\Tour\Business\TourFacadeInterface
     */
    protected $tourFacade;

    /**
     * DrivingLicenceHydrator constructor.
     *
     * @param \Pyz\Zed\Tour\Business\TourFacadeInterface $vehicleFacade
     */
    public function __construct(TourFacadeInterface $vehicleFacade)
    {
        $this->tourFacade = $vehicleFacade;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Orm\Zed\Driver\Persistence\DstDriver $driverEntity
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     * @return void
     */
    public function hydrateDriverByDrivingLicence(
        DstDriver $driverEntity,
        DriverTransfer $driverTransfer
    ) {
        $drivingLicenceTransfer = $this
            ->tourFacade
            ->getDrivingLicenceById($driverEntity->getFkDrivingLicence());
        $driverTransfer->setDrivingLicence($drivingLicenceTransfer);
    }
}
