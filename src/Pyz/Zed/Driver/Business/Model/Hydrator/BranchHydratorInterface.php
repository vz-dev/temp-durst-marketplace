<?php


namespace Pyz\Zed\Driver\Business\Model\Hydrator;


use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Driver\Persistence\DstDriver;

interface BranchHydratorInterface
{
    /**
     * @param DstDriver $driverEntity
     * @param DriverTransfer $driverTransfer
     * @return void
     */
    public function hydrateDriverByBranch(
        DstDriver $driverEntity,
        DriverTransfer $driverTransfer);
}
