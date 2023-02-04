<?php
namespace PyzTest\Functional\Zed\Easybill\Business\Queue;

use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Zed\Easybill\Business\Queue\InvoiceQueueManager;
use Pyz\Zed\Easybill\Dependency\Client\EasybillToQueueBridge;
use Spryker\Client\Queue\QueueClient;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Easybill
 * @group Queue
 * @group InvoiceQueueManagerTest
 * Add your own group annotations below this line
 */
class InvoiceQueueManagerTest extends Unit
{
    /**
     * @var \PyzTest\Functional\Zed\Easybill\EasybillBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\Easybill\Business\Queue\InvoiceQueueManagerInterface
     */
    protected $invoiceQueueManager;

    /**
     * @var MockObject|\Pyz\Zed\Easybill\Dependency\Client\EasybillToQueueBridgeInterface
     */
    protected $queueClientBridgeMock;

    /**
     * @return void
     */
    protected function _before()
    {
        $this->invoiceQueueManager = new InvoiceQueueManager($this->mockQueueClientBridge());
    }

    /**
     * @return void
     */
    protected function _after()
    {
    }

    /**
     * @return MockObject|\Pyz\Zed\Easybill\Dependency\Client\EasybillToQueueBridgeInterface
     */
    protected function mockQueueClientBridge()
    {
        $this->queueClientBridgeMock = $this
            ->getMockBuilder(EasybillToQueueBridge::class)
            ->setConstructorArgs([
                $this->mockQueueClient(),
            ])
            ->setMethods([
                'sendMessage',
            ])
            ->getMock();

        return $this->queueClientBridgeMock;
    }

    /**
     * @return MockObject|\Spryker\Client\Queue\QueueClientInterface
     */
    protected function mockQueueClient()
    {
        return $this
            ->getMockBuilder(QueueClient::class)
            ->setMethods([
                'sendMessage',
            ])
            ->getMock();
    }

    // tests
    /**
     * @return void
     */
    public function testAddReferenceToInvoiceQueueCallsClientMethod()
    {
        $this
            ->queueClientBridgeMock
            ->expects(
                $this->once()
            )
            ->method('sendMessage');

        $this
            ->invoiceQueueManager
            ->addReferenceToInvoiceQueue('1');
    }
}
