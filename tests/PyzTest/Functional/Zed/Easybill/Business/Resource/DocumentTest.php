<?php
namespace PyzTest\Functional\Zed\Easybill\Business\Resource;

use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Pyz\Shared\Easybill\EasybillConstants;
use Pyz\Zed\Easybill\Business\Resource\Document;
use Pyz\Zed\Easybill\Dependency\Service\EasybillToHttpRequestBridgeInterface;
use Pyz\Zed\Easybill\EasybillConfig;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Easybill
 * @group Resource
 * @group DocumentTest
 * Add your own group annotations below this line
 */
class DocumentTest extends Unit
{
    protected const DOCUMENT_ID = 4;
    protected const DOCUMENT_NUMBER = 'DE--22323';

    /**
     * @var \PyzTest\Functional\Zed\Easybill\EasybillBusinessTester
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
     * @return \Pyz\Zed\Easybill\EasybillConfig|object
     */
    protected function mockConfig()
    {
        return $this
            ->makeEmpty(
                EasybillConfig::class,
                [
                    'getEasybillApiUrl',
                    'getEasybillEmail',
                    'getEasybillApiKey',
                ]
            );
    }

    // tests
    /**
     * @return void
     */
    public function testCreateInvoiceCallsClientTwice()
    {
        /** @var \Pyz\Zed\Easybill\Dependency\Service\EasybillToHttpRequestBridgeInterface|object $httpRequestServiceMock */
        $httpRequestServiceMock = $this
            ->makeEmpty(
                EasybillToHttpRequestBridgeInterface::class,
                [
                    'sendRequest' => Expected::exactly(
                        2,
                        (new HttpResponseTransfer())
                            ->setBody(
                                json_encode(
                                    [
                                        'id' => static::DOCUMENT_ID,
                                        'number' => static::DOCUMENT_NUMBER,
                                    ]
                                )
                            )
                            ->setCode(EasybillConstants::CODE_SUCCESS)
                    ),
                ]
            );

        (new Document($httpRequestServiceMock, $this->mockConfig()))
            ->createInvoice(1);
    }
}
