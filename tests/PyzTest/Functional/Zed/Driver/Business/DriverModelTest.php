<?php
namespace PyzTest\Functional\Zed\Driver\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Driver\Persistence\Map\DstDriverTableMap;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Driver\Business\Exception\DriverInvalidArgumentException;
use Pyz\Zed\Driver\Business\Exception\DriverNotExistsException;
use Pyz\Zed\Driver\Business\Model\Driver;
use Pyz\Zed\Driver\Business\Model\Hydrator\BranchHydrator;
use Pyz\Zed\Driver\Business\Model\Hydrator\DrivingLicenseHydrator;
use Pyz\Zed\Driver\Persistence\DriverQueryContainer;
use Pyz\Zed\Merchant\Business\MerchantFacade;
use Pyz\Zed\Tour\Business\TourFacade;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Driver
 * @group DriverModelTest
 * Add your own group annotations below this line
 */
class DriverModelTest extends Unit
{
    protected const DRIVER_FIRST_NAME = 'Fritz';
    protected const DRIVER_LAST_NAME = 'Fahrer';
    protected const DRIVER_ID_BRANCH = 8;
    protected const DRIVER_SALUTATION = DstDriverTableMap::COL_SALUTATION_MR;
    protected const DRIVER_STATUS = DstDriverTableMap::COL_STATUS_INACTIVE;
    protected const DRIVER_EMAIL = 'fritz.fahrer@durst.shop';
    protected const DRIVER_PASSWORD = 'test123';
    protected const DRIVER_UNIQUE_EMAIL = 'fritz.fahrer.unique@durst.shop';

    /**
     * @var \PyzTest\Functional\Zed\Driver\DriverBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\Driver\Business\Model\DriverInterface
     */
    protected $driverModel;

    /**
     * @return void
     */
    protected function _before()
    {
        $this->driverModel = new Driver(
            new DriverQueryContainer(),
            new DrivingLicenseHydrator(
                new TourFacade()
            ),
            new BranchHydrator(
                new MerchantFacade()
            )
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
    public function testGetDriverByIdReturnsCorrectDriver()
    {
        $driverTransfer = $this->driverModel->getDriverById(2);

        $this
            ->assertEquals(
                self::DRIVER_EMAIL,
                $driverTransfer->getEmail()
            );
        $this
            ->assertEquals(
                self::DRIVER_FIRST_NAME,
                $driverTransfer->getFirstName()
            );
        $this
            ->assertEquals(
                self::DRIVER_LAST_NAME,
                $driverTransfer->getLastName()
            );
        $this
            ->assertEquals(
                self::DRIVER_STATUS,
                $driverTransfer->getStatus()
            );
        $this
            ->assertEquals(
                self::DRIVER_SALUTATION,
                $driverTransfer->getSalutation()
            );
        $this
            ->assertEquals(
                self::DRIVER_ID_BRANCH,
                $driverTransfer->getFkBranch()
            );
    }

    /**
     * @return void
     */
    public function getDriverByIdThrowsExceptionOnNonExistentDriver()
    {
        $this
            ->expectException(DriverNotExistsException::class);

        $this
            ->driverModel
            ->getDriverById(9999);
    }

    /**
     * @return void
     */
    public function testGetDriverByIdDoesNotContainClearPassword()
    {
        $driverTransfer = $this
            ->driverModel
            ->getDriverById(2);

        $this
            ->assertNotEquals(
                self::DRIVER_PASSWORD,
                $driverTransfer->getPassword()
            );
    }

    /**
     * @return void
     */
    public function testGetDriversByFkBranchReturnsDriversForBranch()
    {
        $drivers = $this
            ->driverModel
            ->getDriversByFkBranch(8);

        $this
            ->assertCount(
                1,
                $drivers
            );
    }

    /**
     * @return void
     */
    public function testGetDriversByFkBranchReturnsOnlyActiveDrivers()
    {
        $drivers = $this
            ->driverModel
            ->getDriversByFkBranch(8);

        foreach ($drivers as $driver) {
            $this
                ->assertEquals(
                    DstDriverTableMap::COL_STATUS_ACTIVE,
                    $driver->getStatus()
                );
        }
    }

    /**
     * @return void
     */
    public function testGetDriversByFkBranchDoesNotReturnDriversFromDifferentBranch()
    {
        $drivers = $this
            ->driverModel
            ->getDriversByFkBranch(8);

        foreach ($drivers as $driver) {
            $this
                ->assertEquals(
                    8,
                    $driver->getFkBranch()
                );
        }
    }

    /**
     * @return void
     */
    public function testSavePersistsTransferToDatabase()
    {
        $driver = $this
            ->driverModel
            ->save($this->createUniqueDriverTransfer());

        $this
            ->assertEquals(
                self::DRIVER_UNIQUE_EMAIL,
                $driver->getEmail()
            );
        $this
            ->assertEquals(
                self::DRIVER_FIRST_NAME,
                $driver->getFirstName()
            );
        $this
            ->assertEquals(
                self::DRIVER_LAST_NAME,
                $driver->getLastName()
            );
        $this
            ->assertEquals(
                self::DRIVER_STATUS,
                $driver->getStatus()
            );
        $this
            ->assertEquals(
                self::DRIVER_SALUTATION,
                $driver->getSalutation()
            );
        $this
            ->assertEquals(
                self::DRIVER_ID_BRANCH,
                $driver->getFkBranch()
            );
        $this
            ->assertEquals(
                true,
                password_verify(self::DRIVER_PASSWORD, $driver->getPassword())
            );

        $driver = $this
            ->driverModel
            ->getDriverById($driver->getIdDriver());

        $this
            ->assertEquals(
                self::DRIVER_UNIQUE_EMAIL,
                $driver->getEmail()
            );
        $this
            ->assertEquals(
                self::DRIVER_FIRST_NAME,
                $driver->getFirstName()
            );
        $this
            ->assertEquals(
                self::DRIVER_LAST_NAME,
                $driver->getLastName()
            );
        $this
            ->assertEquals(
                self::DRIVER_STATUS,
                $driver->getStatus()
            );
        $this
            ->assertEquals(
                self::DRIVER_SALUTATION,
                $driver->getSalutation()
            );
        $this
            ->assertEquals(
                self::DRIVER_ID_BRANCH,
                $driver->getFkBranch()
            );
    }

    /**
     * @return void
     */
    public function testSaveThrowsExceptionOnDuplicateEmailAddress()
    {
        $this
            ->expectException(PropelException::class);

        $this
            ->driverModel
            ->save($this->createDriverTransfer());
    }

    /**
     * @return void
     */
    public function testSaveThrowsExceptionOnEmptyBranchId()
    {
        $driver = $this
            ->createDriverTransfer();

        $driver->setFkBranch(null);

        $this
            ->expectException(DriverInvalidArgumentException::class);

        $this
            ->driverModel
            ->save($driver);
    }

    /**
     * @return void
     */
    public function testRemoveDriverDeletesCorrectDriver()
    {
        $driver = $this
            ->driverModel
            ->getDriverById(1);

        $this
            ->assertNotEquals(
                DstDriverTableMap::COL_STATUS_DELETED,
                $driver->getStatus()
            );

        $this
            ->driverModel
            ->removeDriver(1);

        $driver = $this
            ->driverModel
            ->getDriverById(1);

        $this
            ->assertEquals(
                DstDriverTableMap::COL_STATUS_DELETED,
                $driver->getStatus()
            );
    }

    /**
     * @return void
     */
    public function testRemoveDriverThrowsExceptionOnNonExistentDriver()
    {
        $this
            ->expectException(DriverNotExistsException::class);

        $this
            ->driverModel
            ->removeDriver(9999);
    }

    /**
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    protected function createDriverTransfer(): DriverTransfer
    {
        return (new DriverTransfer())
            ->setStatus(self::DRIVER_STATUS)
            ->setSalutation(self::DRIVER_SALUTATION)
            ->setFirstName(self::DRIVER_FIRST_NAME)
            ->setPassword(self::DRIVER_PASSWORD)
            ->setLastName(self::DRIVER_LAST_NAME)
            ->setFkBranch(self::DRIVER_ID_BRANCH)
            ->setFkDrivingLicence(1)
            ->setEmail(self::DRIVER_EMAIL);
    }

    /**
     * @return \Generated\Shared\Transfer\DriverTransfer
     */
    protected function createUniqueDriverTransfer(): DriverTransfer
    {
        return (new DriverTransfer())
            ->setStatus(self::DRIVER_STATUS)
            ->setSalutation(self::DRIVER_SALUTATION)
            ->setFirstName(self::DRIVER_FIRST_NAME)
            ->setPassword(self::DRIVER_PASSWORD)
            ->setLastName(self::DRIVER_LAST_NAME)
            ->setFkBranch(self::DRIVER_ID_BRANCH)
            ->setFkDrivingLicence(1)
            ->setEmail(self::DRIVER_UNIQUE_EMAIL);
    }
}
