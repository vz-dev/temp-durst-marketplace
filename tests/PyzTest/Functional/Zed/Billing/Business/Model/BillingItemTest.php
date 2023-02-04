<?php
/**
 * Durst - project - BillingItemTest.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-03-05
 * Time: 20:53
 */

namespace PyzTest\Functional\Zed\Billing\Business\Model;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\BillingItemTransfer;
use Generated\Shared\Transfer\BillingPeriodTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\Business\Model\BillingItem;
use Pyz\Zed\Billing\Business\Model\BillingPeriod;
use Pyz\Zed\Billing\Persistence\BillingQueryContainer;
use PyzTest\Functional\Zed\Billing\BillingMocksTrait;

class BillingItemTest extends Unit
{
    use BillingMocksTrait;

    const TEST_BILLING_ITEM_FKSALESORDER = 1;
    const TEST_BILLING_ITEM_AMOUNT = 10000;
    const TEST_BILLING_ITEM_TAX = 1900;
    const TEST_BILLING_ITEM_DISCOUNT = 2000;
    const TEST_BILLING_ITEM_RETURN_DEPOSIT = 1000;
    const TEST_ID_BILLINGPERIOD = 3;
    /**
     * @var BillingQueryContainer
     */
    protected $queryContainer;

    /**
     * @var BillingConfig
     */
    protected $config;

    /**
     * @var BillingPeriod
     */
    protected $billingPeriodModel;

    /**
     * @var BillingItem
     */
    protected $billingItemModel;

    protected function _before()
    {
        $this->queryContainer = $this->createBillingQueryContainerMock();
        $this->config = $this->createBillingConfigMock();
        $this->billingPeriodModel = $this->createBillingPeriodMock();

        $this->billingItemModel = new BillingItem(
            $this->config,
            $this->queryContainer,
            $this->billingPeriodModel
        );
    }

    protected function _after()
    {

    }

    /**
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function testCreateBillingItemEntityFromTransfer()
    {
        $testBillingItemTransfer = $this->createTestBillingItem();

        $billingItemMock = $this->createBillingItemMock();

        $billingItemMock
            ->expects($this->atLeastOnce())
            ->method('createBillingEntity')
            ->with($this->createTestBillingItem());

        $billingItemMock->createBillingItem($testBillingItemTransfer);
    }

    protected function createTestBillingItem()
    {
        return (new BillingItemTransfer())
            ->setBillingPeriod($this->createTestBillingPeriodTransfer())
            ->setFkSalesOrder(self::TEST_BILLING_ITEM_FKSALESORDER)
            ->setAmount(self::TEST_BILLING_ITEM_AMOUNT)
            ->setTaxAmount(self::TEST_BILLING_ITEM_TAX)
            ->setDiscountAmount(self::TEST_BILLING_ITEM_DISCOUNT)
            ->setReturnDepositAmount(self::TEST_BILLING_ITEM_RETURN_DEPOSIT);
    }

    protected function createTestBillingPeriodTransfer()
    {
        return (new BillingPeriodTransfer())
            ->setIdBillingPeriod(123)
            ->setBillingReference('DBP-987')
            ->setBranch((new BranchTransfer())->setIdBranch(1))
            ->setStartDate('2020-01-01')
            ->setEndDate('2020-01-31');
    }
}
