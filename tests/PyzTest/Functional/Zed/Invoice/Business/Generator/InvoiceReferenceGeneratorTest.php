<?php
namespace PyzTest\Functional\Zed\Invoice\Business\Generator;

use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Invoice\Business\Generator\InvoiceReferenceGenerator;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToMerchantBridgeInterface;
use Pyz\Zed\Invoice\Dependency\Facade\InvoiceToSequenceNumberBridgeInterface;
use Pyz\Zed\Invoice\InvoiceConfig;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Invoice
 * @group Generator
 * @group InvoiceReferenceGeneratorTest
 * Add your own group annotations below this line
 */
class InvoiceReferenceGeneratorTest extends Unit
{
    protected const MERCHANT_ID = 413;

    /**
     * @var \PyzTest\Functional\Zed\Invoice\InvoiceBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    protected function _before()
    {
    }

    /**
     * @return void
     */
    protected function _after()
    {
    }

    /**
     * @return \Pyz\Zed\Invoice\InvoiceConfig|object
     */
    protected function mockConfig()
    {
        return $this
            ->makeEmpty(
                InvoiceConfig::class,
                [
                    'getInvoiceSequenceNumberSettingsTransfer',
                ]
            );
    }

    // tests
    /**
     * @return void
     */
    public function testCreateInvoiceReferenceCallsMerchantAndSequenceNumberFacade()
    {
        /** @var \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToSequenceNumberBridgeInterface|object $sequenceNumberFacadeMock */
        $sequenceNumberFacadeMock = $this
            ->makeEmpty(
                InvoiceToSequenceNumberBridgeInterface::class,
                [
                    'generate' => Expected::atLeastOnce('DE-AB-10'),
                ]
            );
        /** @var \Pyz\Zed\Invoice\Dependency\Facade\InvoiceToMerchantBridgeInterface|object $merchantFacadeMock */
        $merchantFacadeMock = $this
            ->makeEmpty(
                InvoiceToMerchantBridgeInterface::class,
                [
                    'getBranchById' => Expected::atLeastOnce(
                        (new BranchTransfer())
                            ->setIdBranch(1)
                            ->setFkMerchant(static::MERCHANT_ID)
                    ),
                ]
            );

        (new InvoiceReferenceGenerator(
            $this->mockConfig(),
            $merchantFacadeMock,
            $sequenceNumberFacadeMock
        ))
        ->createInvoiceReference((new OrderTransfer())->setFkBranch(1));
    }
}
