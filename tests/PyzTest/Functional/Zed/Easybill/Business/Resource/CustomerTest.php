<?php
namespace PyzTest\Functional\Zed\Easybill\Business\Resource;

use ArrayObject;
use Codeception\Stub\Expected;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Pyz\Shared\Easybill\EasybillConstants;
use Pyz\Zed\Easybill\Business\Exception\EasybillException;
use Pyz\Zed\Easybill\Business\Resource\Customer;
use Pyz\Zed\Easybill\Dependency\Service\EasybillToHttpRequestBridgeInterface;
use Pyz\Zed\Easybill\EasybillConfig;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Easybill
 * @group Resource
 * @group CustomerTest
 * Add your own group annotations below this line
 */
class CustomerTest extends Unit
{
    protected const CUSTOMER_ID = 1;

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
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function createCustomerTransfer(): CustomerTransfer
    {
        return (new CustomerTransfer())
            ->setFirstName('Mathias')
            ->setLastName('Bicker')
            ->setEmail('test@test.com')
            ->setCompany('Durststrecke GmbH')
            ->setPhone('0221')
            ->setBillingAddress(
                new ArrayObject([
                    (new AddressTransfer())
                        ->setZipCode('50825')
                        ->setIso2Code('DE')
                        ->setAddress1('Teststraße 1'),
                ])
            );
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
    public function testCreateCustomerCallsServiceAndReturnsId()
    {
        /** @var \Pyz\Zed\Easybill\Dependency\Service\EasybillToHttpRequestBridgeInterface|object $httpRequestServiceMock */
        $httpRequestServiceMock = $this
            ->makeEmpty(
                EasybillToHttpRequestBridgeInterface::class,
                [
                    'sendRequest' => Expected::atLeastOnce(
                        (new HttpResponseTransfer())
                        ->setBody(
                            json_encode(
                                [
                                    'id' => static::CUSTOMER_ID,
                                ]
                            )
                        )
                        ->setCode(EasybillConstants::CODE_SUCCESS)
                    ),
                ]
            );

        $customerId = (new Customer($httpRequestServiceMock, $this->mockConfig()))
            ->createCustomer($this->createCustomerTransfer());

        $this
            ->assertSame(static::CUSTOMER_ID, $customerId);
    }

    /**
     * @return void
     */
    public function testCreateCustomerThrowsExceptionUponInvalidCustomerResponseCode()
    {
        /** @var \Pyz\Zed\Easybill\Dependency\Service\EasybillToHttpRequestBridgeInterface|object $httpRequestServiceMock */
        $httpRequestServiceMock = $this
            ->makeEmpty(
                EasybillToHttpRequestBridgeInterface::class,
                [
                    'sendRequest' => Expected::atLeastOnce(
                        (new HttpResponseTransfer())
                            ->setBody(
                                json_encode(
                                    [
                                        'id' => static::CUSTOMER_ID,
                                    ]
                                )
                            )
                            ->setCode(EasybillConstants::CODE_INVALID_CUSTOMER)
                    ),
                ]
            );

        $this
            ->expectException(EasybillException::class);

        (new Customer($httpRequestServiceMock, $this->mockConfig()))
            ->createCustomer($this->createCustomerTransfer());
    }
}
