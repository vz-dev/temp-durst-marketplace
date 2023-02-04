<?php
namespace PyzTest\Functional\Zed\Driver\Business\Hydrator;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Driver\Persistence\DstDriver;
use Orm\Zed\Driver\Persistence\DstDriverQuery;
use Pyz\Zed\Driver\Business\Model\Driver;
use Pyz\Zed\Driver\Business\Model\Hydrator\BranchHydrator;
use Pyz\Zed\Driver\Business\Model\Hydrator\BranchHydratorInterface;
use Pyz\Zed\Driver\Business\Model\Hydrator\DrivingLicenseHydrator;
use Pyz\Zed\Driver\Persistence\DriverQueryContainer;
use Pyz\Zed\Merchant\Business\MerchantFacade;
use Pyz\Zed\Tour\Business\TourFacade;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Driver
 * @group Hydrator
 * @group DrivingLicenseHydratorTest
 * Add your own group annotations below this line
 */
class DrivingLicenseHydratorTest extends Unit
{
    protected const DRIVER_ID = 1;
    protected const DRIVING_LICENCE_ID = 1;
    protected const DRIVING_LICENCE_CODE = 'A';
    protected const DRIVING_LICENCE_NAME = 'Klasse A';
    protected const DRIVING_LICENCE_DESCRIPTION = 'Motorrad';

    /**
     * @var \PyzTest\Functional\Zed\Driver\DriverBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\Driver\Business\Model\Hydrator\DrivingLicenseHydratorInterface
     */
    protected $drivingLicenseHydrator;

    /**
     * @var BranchHydratorInterface
     */
    protected $branchHydrator;

    /**
     * @return void
     */
    protected function _before()
    {
        $this
            ->drivingLicenseHydrator = new DrivingLicenseHydrator(
                new TourFacade()
            );

        $this
            ->branchHydrator = new BranchHydrator(
                new MerchantFacade()
            );
    }

    /**
     * @return void
     */
    protected function _after()
    {
    }

    /**
     * @return void
     */
    public function testHydrateDriverByDrivingLicenceHydratesDriverWithDrivingLicence()
    {
        $driverEntity = $this
            ->getDriverEntity();

        $driverTransfer = $this
            ->getDriverTransfer();

        $this
            ->assertEquals(
                1,
                $driverEntity->getFkDrivingLicence()
            );

        $this
            ->assertNull(
                $driverTransfer->getDrivingLicence()
            );

        $this
            ->drivingLicenseHydrator
            ->hydrateDriverByDrivingLicence($driverEntity, $driverTransfer);

        $this
            ->assertNotNull($driverTransfer->getDrivingLicence());
        $this
            ->assertEquals(
                self::DRIVING_LICENCE_ID,
                $driverTransfer->getDrivingLicence()->getIdDrivingLicence()
            );
        $this
            ->assertEquals(
                self::DRIVING_LICENCE_NAME,
                $driverTransfer->getDrivingLicence()->getName()
            );
        $this
            ->assertEquals(
                self::DRIVING_LICENCE_CODE,
                $driverTransfer->getDrivingLicence()->getCode()
            );
        $this
            ->assertEquals(
                self::DRIVING_LICENCE_DESCRIPTION,
                $driverTransfer->getDrivingLicence()->getDescription()
            );
    }

    /**
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    protected function getDriverTransfer(): DriverTransfer
    {
        $driverTransfer = (new Driver(
            new DriverQueryContainer(),
            $this->drivingLicenseHydrator,
            $this->branchHydrator
        ))
        ->getDriverById(self::DRIVER_ID);

        $driverTransfer->setDrivingLicence(null);

        return $driverTransfer;
    }

    /**
     * @return \Orm\Zed\Driver\Persistence\DstDriver
     */
    protected function getDriverEntity(): DstDriver
    {
        return DstDriverQuery::create()
            ->findOneByIdDriver(self::DRIVER_ID);
    }
}
