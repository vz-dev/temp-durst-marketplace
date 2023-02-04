<?php
/**
 * Durst - project - BillingPeriodTest.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-03-05
 * Time: 20:53
 */

namespace PyzTest\Functional\Zed\Billing\Business\Model;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Orm\Zed\Billing\Persistence\DstBillingPeriodQuery;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Zed\Billing\Business\Exception\BillingPeriodEntityNotFoundException;
use Pyz\Zed\Billing\Business\Model\BillingPeriod;
use Pyz\Zed\Billing\Business\Model\File\DownloadManagerInterface;
use Pyz\Zed\Billing\Persistence\BillingQueryContainer;
use PyzTest\Functional\Zed\Billing\BillingMocksTrait;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Billing
 * @group Business
 * @group Model
 * @group BillingPeriodTest
 * Add your own group annotations below this line
 */
class BillingPeriodTest extends Unit
{
    use BillingMocksTrait;

    const BILLINGREFERENCE = 'DBP-3';
    const STARTDATE = '2020-03-05';
    const ENDDATE = '2020-03-12';
    const IDBRANCH = 1;

    const DATE_FORMAT = 'Y-m-d';
    const INVALID_IDINVOICEPERIOD = 99999;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\BillingPeriod
     */
    protected $billingPeriodModel;

    /**
     * @var \Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridge|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $merchantFacade;

    /**
     * @var \Pyz\Zed\Billing\Persistence\BillingQueryContainer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $queryContainer;

    /**
     * @var DownloadManagerInterface|MockObject
     */
    protected $downloadManager;

    /**
     * @return void
     */
    protected function _before()
    {
        $this->queryContainer = new BillingQueryContainer();
        $this->merchantFacade = $this->createBillingToMerchantBridgeMock();
        $this->downloadManager = $this->createDownloadManagerMock();

        $this->billingPeriodModel = new BillingPeriod(
            $this->queryContainer,
            $this->merchantFacade,
            $this->downloadManager
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
    public function testCreateBillingPeriodFromTransfer()
    {
        $billingPeriod = $this->billingPeriodModel->createBillingPeriod($this->createTestBillingPeriodTransfer());

        $billingPeriodEntity = DstBillingPeriodQuery::create()
            ->filterByEndDate(self::ENDDATE)
            ->filterByStartDate(self::STARTDATE)
            ->filterByFkBranch(self::IDBRANCH)
            ->filterByBillingReference(self::BILLINGREFERENCE)
            ->findOne();

        $this->assertNotNull($billingPeriodEntity);

        /**
         * entity is correctly set with all values
         */
        $this->assertEquals(self::BILLINGREFERENCE, $billingPeriodEntity->getBillingReference());
        $this->assertEquals(self::STARTDATE, $billingPeriodEntity->getStartDate()->format(self::DATE_FORMAT));
        $this->assertEquals(self::ENDDATE, $billingPeriodEntity->getEndDate()->format(self::DATE_FORMAT));
        $this->assertEquals(self::IDBRANCH, $billingPeriodEntity->getFkBranch());

        // returns correctly hydrated transfer
        $this->assertEquals(self::BILLINGREFERENCE, $billingPeriod->getBillingReference());
        $this->assertEquals(self::STARTDATE, $billingPeriod->getStartDate());
        $this->assertEquals(self::ENDDATE, $billingPeriod->getEndDate());
    }

    /**
     * @return void
     */
    public function testGetBillingPeriodById()
    {
        $testBillingPeriodTransfers = $this->createThreeTestBillingPeriods();

        $billingPeriod = $this->billingPeriodModel->getBillingPeriodById($testBillingPeriodTransfers[1]->getIdBillingPeriod());

        $this->assertNotNull($billingPeriod);

        $this->assertEquals($testBillingPeriodTransfers[1]->getIdBillingPeriod(), $billingPeriod->getIdBillingPeriod());
    }

    /**
     * @return void
     */
    public function testNoBillingPeriodFoundByIdThrowsException()
    {
        $this->expectException(BillingPeriodEntityNotFoundException::class);

        $this->billingPeriodModel->getBillingPeriodById(self::INVALID_IDINVOICEPERIOD);
    }

    /**
     * @return void
     */
    public function testGetBillingPeriodsByEndDate()
    {
        $testBillingPeriodTransfers = $this->createThreeTestBillingPeriods();

        $billingPeriods = $this->billingPeriodModel->getBillingPeriodsByEndDate($testBillingPeriodTransfers[2]->getEndDate());

        $this->assertNotNull($billingPeriods);

        $this->assertEquals($testBillingPeriodTransfers[2]->getIdBillingPeriod(), $billingPeriods[0]->getIdBillingPeriod());
    }

    /**
     * @return void
     */
    public function testGetBillingPeriodsByEndDateNotFoundReturnsEmpty()
    {
        $testBillingPeriodTransfers = $this->createThreeTestBillingPeriods();

        $billingPeriods = $this->billingPeriodModel->getBillingPeriodsByEndDate('2020-01-01');

        $this->assertEmpty($billingPeriods);
    }

    /**
     * @return void
     */
    public function testGetBillingPeriodByEndDateForBranch()
    {
        $testBillingPeriodTransfers = $this->createThreeTestBillingPeriods();

        $billingPeriod = $this->billingPeriodModel->getBillingPeriodByEndDateForBranchId($testBillingPeriodTransfers[2]->getEndDate(), $testBillingPeriodTransfers[2]->getBranch()->getIdBranch());

        $this->assertNotNull($billingPeriod);

        $this->assertEquals($testBillingPeriodTransfers[2]->getEndDate(), $billingPeriod->getEndDate());
        $this->assertEquals($testBillingPeriodTransfers[2]->getBranch()->getIdBranch(), $billingPeriod->getBranch()->getIdBranch());
    }

    /**
     * @return void
     */
    public function testGetCurrentBillingPeriodForBranchById()
    {
        $testBillingPeriodTransfer = $this->createTestCurrentBillingPeriod();

        $billingPeriod = $this->billingPeriodModel->getCurrentBillingPeriodForBranchById($testBillingPeriodTransfer->getBranch()->getIdBranch());

        $this->assertNotNull($billingPeriod);

        $this->assertEquals($testBillingPeriodTransfer->getEndDate(), $billingPeriod->getEndDate());
        $this->assertEquals($testBillingPeriodTransfer->getStartDate(), $billingPeriod->getStartDate());
        $this->assertEquals($testBillingPeriodTransfer->getBranch()->getIdBranch(), $billingPeriod->getBranch()->getIdBranch());
    }

    /**
     * @return void
     */
    public function testGetCurrentBillingPeriodForBranchByIdReturnsNullNoResults()
    {
        $testBillingPeriodTransfer = $this->createTestFutureBillingPeriod();

        $billingPeriod = $this->billingPeriodModel->getCurrentBillingPeriodForBranchById($testBillingPeriodTransfer->getBranch()->getIdBranch());

        $this->assertNull($billingPeriod);
    }

    /**
     * @return BillingPeriodTransfer
     */
    protected function createTestBillingPeriodTransfer() : BillingPeriodTransfer
    {
        return (new BillingPeriodTransfer())
            ->setBranch($this->createTestBranchTransfer())
            ->setBillingReference(self::BILLINGREFERENCE)
            ->setStartDate(self::STARTDATE)
            ->setEndDate(self::ENDDATE);
    }

    /**
     * @return BranchTransfer
     */
    protected function createTestBranchTransfer() :BranchTransfer
    {
        $this
            ->merchantFacade
            ->expects($this->atLeastOnce())
            ->method('getBranchById')
            ->willReturn((new BranchTransfer())->setIdBranch(self::IDBRANCH));

        return (new BranchTransfer())
            ->setIdBranch(self::IDBRANCH);
    }

    /**
     * @return array
     */
    protected function createThreeTestBillingPeriods() : array
    {
        $billingTransfers = [];

        $billingTransfers[] = (new BillingPeriodTransfer())
            ->setStartDate('2020-03-09')
            ->setEndDate('2020-03-14')
            ->setBillingReference('DBP-1')
            ->setBranch($this->createTestBranchTransfer());

        $billingTransfers[] = (new BillingPeriodTransfer())
            ->setStartDate('2020-03-15')
            ->setEndDate('2020-03-19')
            ->setBillingReference('DBP-2')
            ->setBranch($this->createTestBranchTransfer());

        $billingTransfers[] = (new BillingPeriodTransfer())
            ->setStartDate('2020-03-01')
            ->setEndDate('2020-03-07')
            ->setBillingReference('DBP-3')
            ->setBranch($this->createTestBranchTransfer());

        $returnTransfers = [];
        foreach ($billingTransfers as $billingTransfer) {
            $returnTransfers[] = $this->billingPeriodModel->createBillingPeriod($billingTransfer);
        }

        return $returnTransfers;
    }

    /**
     * @return BillingPeriodTransfer
     */
    protected function createTestCurrentBillingPeriod() : BillingPeriodTransfer
    {
        $billingTransfer = (new BillingPeriodTransfer())
            ->setStartDate(date('Y-m-d', strtotime('-7 days')))
            ->setEndDate(date('Y-m-d', strtotime('+7 days')))
            ->setBillingReference('DBP-99')
            ->setBranch($this->createTestBranchTransfer());

        return $this->billingPeriodModel->createBillingPeriod($billingTransfer);
    }

    protected function createTestFutureBillingPeriod()
    {
        $billingTransfer = (new BillingPeriodTransfer())
            ->setStartDate(date('Y-m-d', strtotime('+14 days')))
            ->setEndDate(date('Y-m-d', strtotime('+21 days')))
            ->setBillingReference('DBP-99')
            ->setBranch($this->createTestBranchTransfer());

        return $this->billingPeriodModel->createBillingPeriod($billingTransfer);
    }
}
