<?php
namespace PyzTest\Functional\Zed\Easybill\Business\Resource;

use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use Pyz\Zed\Easybill\Business\Resource\CustomerInterface;
use Pyz\Zed\Easybill\Business\Resource\DocumentInterface;
use Pyz\Zed\Easybill\Business\Resource\ResourceManager;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Easybill
 * @group Resource
 * @group ResourceManagerTest
 * Add your own group annotations below this line
 */
class ResourceManagerTest extends Unit
{
    protected const INVOICE_NUMBER = 'D-231231';

    /**
     * @var \PyzTest\Functional\Zed\Easybill\EasybillBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\Easybill\Business\Resource\DocumentInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $documentMock;

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

    // tests
    /**
     * @return void
     */
    public function testCreateInvoiceCallsDocumentAndReturnsTrue()
    {
        /** @var \Pyz\Zed\Easybill\Business\Resource\DocumentInterface|object $documentMock */
        $documentMock = $this
            ->makeEmpty(
                DocumentInterface::class,
                [
                    'createInvoice' => Expected::atLeastOnce(static::INVOICE_NUMBER),
                ]
            );

        /** @var \Pyz\Zed\Easybill\Business\Resource\CustomerInterface|object $customerMock */
        $customerMock = $this
            ->makeEmpty(
                CustomerInterface::class,
                [
                    'createCustomer' => Expected::atLeastOnce(1),
                ]
            );

        $resourceManager = new ResourceManager(
            $documentMock,
            $customerMock
        );

        $resourceManager
            ->createInvoice();
    }
}
