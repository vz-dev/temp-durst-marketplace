<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 23.08.18
 * Time: 11:14
 */

namespace Pyz\Zed\Driver\Business;

use Pyz\Zed\Driver\Business\Model\Driver;
use Pyz\Zed\Driver\Business\Model\Hydrator\BranchHydrator;
use Pyz\Zed\Driver\Business\Model\Hydrator\BranchHydratorInterface;
use Pyz\Zed\Driver\Business\Model\Hydrator\DrivingLicenseHydrator;
use Pyz\Zed\Driver\Business\Model\Hydrator\DrivingLicenseHydratorInterface;
use Pyz\Zed\Driver\DriverDependencyProvider;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Merchant\MerchantDependencyProvider;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Pyz\Zed\Driver\DriverConfig getConfig()
 * @method \Pyz\Zed\Driver\Persistence\DriverQueryContainerInterface getQueryContainer()
 */
class DriverBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Driver\Business\Model\Driver
     */
    public function createDriverModel(): Driver
    {
        return new Driver(
            $this->getQueryContainer(),
            $this->createDrivingLicenseHydrator(),
            $this->createBranchHydrator()
        );
    }

    /**
     * @return \Pyz\Zed\Tour\Business\TourFacadeInterface
     */
    protected function getVehicleFacade(): TourFacadeInterface
    {
        return $this->getProvidedDependency(DriverDependencyProvider::FACADE_TOUR);
    }

    /**
     * @return TourFacadeInterface
     */
    protected function getMerchantFacade():MerchantFacadeInterface
    {
        return $this->getProvidedDependency(DriverDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return \Pyz\Zed\Driver\Business\Model\Hydrator\DrivingLicenseHydratorInterface
     */
    protected function createDrivingLicenseHydrator(): DrivingLicenseHydratorInterface
    {
        return new DrivingLicenseHydrator(
            $this->getVehicleFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Driver\Business\Model\Hydrator\BranchHydratorInterface
     */
    protected function createBranchHydrator(): BranchHydratorInterface
    {
        return new BranchHydrator(
            $this->getMerchantFacade()
        );
    }
}
