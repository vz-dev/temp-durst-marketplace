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

interface DrivingLicenseHydratorInterface
{
    /**
     * @param DstDriver $driverEntity
     * @param DriverTransfer $driverTransfer
     * @return void
     */
    public function hydrateDriverByDrivingLicence(
        DstDriver $driverEntity,
        DriverTransfer $driverTransfer);
}
