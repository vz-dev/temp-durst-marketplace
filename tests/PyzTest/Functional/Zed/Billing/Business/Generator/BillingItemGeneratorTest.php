<?php
/**
 * Durst - project - BillingItemGeneratorTest.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-03-10
 * Time: 08:38
 */

namespace PyzTest\Functional\Zed\Billing\Business\Generator;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\TaxTotalTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Zed\Billing\Business\Generator\BillingItemGenerator;
use PyzTest\Functional\Zed\Billing\BillingMocksTrait;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Billing
 * @group Business
 * @group Generator
 * @group BillingItemGeneratorTest
 * Add your own group annotations below this line
 */
class BillingItemGeneratorTest extends Unit
{
    use BillingMocksTrait;

    /**
     * @var \Pyz\Zed\Billing\BillingConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\BillingPeriod|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $billingPeriod;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\BillingItem|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $billingItem;

    /**
     * @var \Pyz\Zed\Billing\Business\Calculator\InvoicesCalculator
     */
    protected $invoicesCalculator;

    /**
     * @var \Pyz\Zed\Billing\Dependency\Facade\BillingToSalesBridgeInterface|MockObject
     */
    protected $salesFacade;

    /**
     * @var \Pyz\Zed\Billing\Business\Generator\BillingItemGeneratorInterface
     */
    protected $billingItemGenerator;

    /**
     * @return void
     */
    protected function _before()
    {
        $this->config = $this->createBillingConfigMock();
        $this->billingPeriod = $this->createBillingPeriodMock();
        $this->billingItem = $this->createBillingItemMock();
        $this->invoicesCalculator = $this->createInvoiceCalculatorMock();
        $this->salesFacade = $this->createBillingToSalesBridgeMock();

        $this->billingItemGenerator = new BillingItemGenerator(
            $this->config,
            $this->billingPeriod,
            $this->billingItem,
            $this->invoicesCalculator,
            $this->salesFacade
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
    public function testCreateBillingItemForEndedBillingPeriods()
    {
        $this
            ->billingPeriod
            ->expects($this->once())
            ->method('getBillingPeriodsByEndDate')
            ->willReturn($this->createTestBillingPeriods());

        $this
            ->salesFacade
            ->expects($this->atLeastOnce())
            ->method('getOrdersByInvoiceDateBetweenStartAndEndDateForBranchId')
            ->willReturn($this->createTestOrderOne());

        $this->billingItemGenerator->createBillingItemsForEndedBillingPeriods();
    }

    protected function createTestBillingPeriods()
    {
        $billingPeriods = [];

        $billingPeriods[] = (new BillingPeriodTransfer())
            ->setBillingReference('DBP-1')
            ->setStartDate(date('Y-m-d', strtotime('-7 days')))
            ->setEndDate(date('Y-m-d', strtotime('yesterday')))
            ->setBranch($this->createBranchTransfer());

        $billingPeriods[] = (new BillingPeriodTransfer())
            ->setBillingReference('DBP-2')
            ->setStartDate(date('Y-m-d', strtotime('-14 days')))
            ->setEndDate(date('Y-m-d', strtotime('yesterday')))
            ->setBranch($this->createBranchTransfer());

        return $billingPeriods;
    }

    protected function createTestOrderOne()
    {
        return [(new OrderTransfer())
            ->setFkBranch($this->createBranchTransfer()->getIdBranch())
            ->setTotals(
                (new TotalsTransfer())
                    ->setGrandTotal(30000)
                    ->setTaxTotal(
                        (new TaxTotalTransfer())
                            ->setAmount(1000)
                    )
                    ->setDiscountTotal(6900)
            )];
    }

    protected function createBranchTransfer()
    {
        return (new BranchTransfer())
            ->setIdBranch(1);
    }
}
