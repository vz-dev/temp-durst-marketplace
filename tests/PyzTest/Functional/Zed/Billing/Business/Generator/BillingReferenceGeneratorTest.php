<?php
/**
 * Durst - project - BillingReferenceGeneratorTest.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-03-05
 * Time: 14:29
 */

namespace PyzTest\Functional\Zed\Billing\Business\Generator;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\Business\Generator\BillingReferenceGenerator;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSequenceNumberBridge;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSequenceNumberBridgeInterface;
use PyzTest\Functional\Zed\Billing\BillingMocksTrait;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacade;

class BillingReferenceGeneratorTest extends Unit
{
    use BillingMocksTrait;

    public const SEQUENCE_NAME = 'BILLING-';
    public const SEQUENCE_PREFIX = 'DBP-';

    /**
     * @var BillingReferenceGenerator|MockObject
     */
    protected $billingReferenceGenerator;

    /**
     * @var BillingConfig|MockObject
     */
    protected $config;

    /**
     * @var BillingToMerchantBridgeInterface|MockObject
     */
    protected $merchantFacade;

    /**
     * @var BillingToSequenceNumberBridgeInterface
     */
    protected $sequenceNumberFacade;


    protected function _before()
    {
        $this->config = $this->createBillingConfigMock();
        $this->merchantFacade = $this->createBillingToMerchantBridgeMock();
        $this->sequenceNumberFacade = $this->createSequenceNumberBridge();


        $this->billingReferenceGenerator = new BillingReferenceGenerator(
            $this->config,
            $this->merchantFacade,
            $this->sequenceNumberFacade
        );
    }


    protected function _after()
    {
    }

    public function testCreateBillingReferenceFromBranchId()
    {
        $this
            ->merchantFacade
            ->expects($this->once())
            ->method('getBranchById')
            ->willReturn($this->createTestBranchTransfer());

        $this
            ->merchantFacade
            ->expects($this->once())
            ->method('getMerchantById')
            ->willReturn($this->createTestMerchantTransfer());

        $this
            ->config
            ->expects($this->atLeastOnce())
            ->method('getBillingSequenceNumberSettingsTransfer')
            ->willReturn($this->createTestSequenceTransfer());

        $billingRef = $this->billingReferenceGenerator->createBillingReferenceFromBranchId(1);


        $this
            ->assertEquals('DBP-1' ,$billingRef);
    }

    public function testCreateTwoBillingReferencesForBranchIncrement()
    {
        $this
            ->merchantFacade
            ->expects($this->atLeastOnce())
            ->method('getBranchById')
            ->willReturn($this->createTestBranchTransfer());

        $this
            ->merchantFacade
            ->expects($this->atLeastOnce())
            ->method('getMerchantById')
            ->willReturn($this->createTestMerchantTransfer());

        $this
            ->config
            ->expects($this->atLeastOnce())
            ->method('getBillingSequenceNumberSettingsTransfer')
            ->willReturn($this->createTestSequenceTransfer()->setName(static::SEQUENCE_NAME.'BRA-1'));

        $billingRef = $this->billingReferenceGenerator->createBillingReferenceFromBranchId(1);
        $this
            ->assertEquals('DBP-1' ,$billingRef);

        $billingRef = $this->billingReferenceGenerator->createBillingReferenceFromBranchId(1);
        $this
            ->assertEquals('DBP-2' ,$billingRef);
    }

    public function testCreateTwoBillingReferencesDifferentBranchesPerBranchIncrement()
    {
        $this
            ->merchantFacade
            ->expects($this->at(0))
            ->method('getBranchById')
            ->willReturn($this->createTestBranchTransfer()->setIdBranch(1));
        $this
            ->merchantFacade
            ->expects($this->at(0))
            ->method('getMerchantById')
            ->willReturn($this->createTestMerchantTransfer()->setBillingPeriodPerBranch(true));

        $this
            ->config
            ->expects($this->at(0))
            ->method('getBillingSequenceNumberSettingsTransfer')
            ->willReturn($this->createTestSequenceTransfer()->setName(static::SEQUENCE_NAME.'BRA-1'));

        $billingRef = $this->billingReferenceGenerator->createBillingReferenceFromBranchId(1);
        $this
            ->assertEquals('DBP-1' ,$billingRef);


        $this
            ->merchantFacade
            ->expects($this->atLeastOnce())
            ->method('getBranchById')
            ->willReturn($this->createTestBranchTransfer()->setIdBranch(2));

        $this
            ->config
            ->expects($this->atLeastOnce())
            ->method('getBillingSequenceNumberSettingsTransfer')
            ->willReturn($this->createTestSequenceTransfer()->setName(static::SEQUENCE_NAME.'BRA-2'));

        $billingRef = $this->billingReferenceGenerator->createBillingReferenceFromBranchId(2);
        $this
            ->assertEquals('DBP-1' ,$billingRef);
    }

    public function testCreateTwoBillingReferencesDifferentBranchesPerMerchantIncrement()
    {
        $this
            ->merchantFacade
            ->expects($this->atLeastOnce())
            ->method('getBranchById')
            ->willReturn($this->createTestBranchTransfer()->setIdBranch(1));
        $this
            ->merchantFacade
            ->expects($this->atLeastOnce())
            ->method('getMerchantById')
            ->willReturn($this->createTestMerchantTransfer()->setBillingPeriodPerBranch(false));

        $this
            ->config
            ->expects($this->atLeastOnce())
            ->method('getBillingSequenceNumberSettingsTransfer')
            ->willReturn($this->createTestSequenceTransfer()->setName(static::SEQUENCE_NAME.'MER-2'));

        $billingRef = $this->billingReferenceGenerator->createBillingReferenceFromBranchId(1);
        $this
            ->assertEquals('DBP-1' ,$billingRef);


        $this
            ->merchantFacade
            ->expects($this->atLeastOnce())
            ->method('getBranchById')
            ->willReturn($this->createTestBranchTransfer()->setIdBranch(2));

        $billingRef = $this->billingReferenceGenerator->createBillingReferenceFromBranchId(2);
        $this
            ->assertEquals('DBP-2' ,$billingRef);
    }

    /**
     * @return BranchTransfer
     */
    protected function createTestBranchTransfer() : BranchTransfer
    {
        return (new BranchTransfer())
            ->setIdBranch(1)
            ->setFkMerchant(2);
    }

    /**
     * @return MerchantTransfer
     */
    protected function createTestMerchantTransfer() : MerchantTransfer
    {
        return (new MerchantTransfer())
            ->setIdMerchant(2)
            ->setBillingPeriodPerBranch(true);
    }

    protected function createTestSequenceTransfer() : SequenceNumberSettingsTransfer
    {
        return (new SequenceNumberSettingsTransfer())
            ->setName(static::SEQUENCE_NAME)
            ->setPrefix(static::SEQUENCE_PREFIX);
    }

    protected function createSequenceNumberBridge()
    {
        return new BillingToSequenceNumberBridge(
            new SequenceNumberFacade()
        );
    }
}
