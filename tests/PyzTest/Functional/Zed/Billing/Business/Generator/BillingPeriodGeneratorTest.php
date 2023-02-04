<?php
/**
 * Durst - project - BillingPeriodGeneratorTest.php.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-28
 * Time: 08:50
 */

namespace PyzTest\Functional\Zed\Billing\Business\Generator;

use Codeception\Test\Unit;
use Orm\Zed\Merchant\Persistence\SpyBranchQuery;
use Pyz\Zed\Billing\Business\Generator\BillingPeriodGenerator;
use Pyz\Zed\Billing\Persistence\BillingQueryContainer;
use PyzTest\Functional\Zed\Billing\BillingMocksTrait;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Billing
 * @group Business
 * @group Generator
 * @group BillingPeriodGeneratorTest
 * Add your own group annotations below this line
 */
class BillingPeriodGeneratorTest extends Unit
{
    use BillingMocksTrait;

    /**
     * @var \Pyz\Zed\Billing\Business\Generator\BillingPeriodGenerator
     */
    protected $billingPeriodGenerator;

    /**
     * @var \Pyz\Zed\Billing\BillingConfig|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $billingConfig;

    /**
     * @var \Pyz\Zed\Billing\Persistence\BillingQueryContainer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $billingQueryContainer;

    /**
     * @var \Pyz\Zed\Billing\Business\Model\BillingPeriod|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $billingPeriod;

    /**
     * @var \Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridge|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $merchantFacade;

    /**
     * @var \Pyz\Zed\Billing\Business\Generator\BillingReferenceGenerator|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $billingReferenceGenerator;

    /**
     * @var \Pyz\Zed\Billing\Dependency\Persistence\BillingToMerchantQueryContainerBridge|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $merchantQuery;

    /**
     * @return void
     */
    protected function _before()
    {
        $this->billingConfig = $this->createBillingConfigMock();
        $this->billingQueryContainer = $this->createBillingQueryContainer();
        $this->billingPeriod = $this->createBillingPeriodMock();
        $this->merchantFacade = $this->createBillingToMerchantBridgeMock();
        $this->billingReferenceGenerator = $this->createBillingReferenceGeneratorMock();
        $this->merchantQuery = $this->createBillingToMerchantQueryContainerMock();

        $this->billingPeriodGenerator = new BillingPeriodGenerator(
            $this->billingConfig,
            $this->billingQueryContainer,
            $this->billingPeriod,
            $this->merchantFacade,
            $this->billingReferenceGenerator,
            $this->merchantQuery
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
    public function testCreateNewBillingPeriods()
    {
        $this
            ->merchantQuery
            ->expects($this->atLeastOnce())
            ->method('queryBranch')
            ->willReturn(
                SpyBranchQuery::create()
            );

        $this->billingPeriodGenerator->createBillingPeriods();
    }

    protected function createBillingQueryContainer()
    {
        return new BillingQueryContainer();
    }

    protected function addTestBranchWithBillingStartToday()
    {
        $branch = SpyBranchQuery::create()
            ->filterByIdBranch(1)
            ->findOne();

        $branch
            ->setBillingEndOfMonth(true)
            ->setBillingCycle('7 days')
            ->setBillingStartDate(date('Y-m-d'))
            ->save();

        return $branch;
    }
}
