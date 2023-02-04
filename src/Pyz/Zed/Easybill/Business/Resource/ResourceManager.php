<?php
/**
 * Durst - project - ResourceManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.01.20
 * Time: 16:36
 */

namespace Pyz\Zed\Easybill\Business\Resource;

use ArrayObject;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Pyz\Zed\Easybill\Business\Exception\TooManyRequestException;

class ResourceManager implements ResourceManagerInterface
{
    /**
     * @var \Pyz\Zed\Easybill\Business\Resource\DocumentInterface
     */
    protected $document;

    /**
     * @var \Pyz\Zed\Easybill\Business\Resource\CustomerInterface
     */
    protected $customer;

    /**
     * ResourceManager constructor.
     *
     * @param \Pyz\Zed\Easybill\Business\Resource\DocumentInterface $document
     * @param \Pyz\Zed\Easybill\Business\Resource\CustomerInterface $customer
     */
    public function __construct(
        DocumentInterface $document,
        CustomerInterface $customer
    ) {
        $this->document = $document;
        $this->customer = $customer;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function createInvoice(): bool
    {
        try {
            $customerId = $this
                ->customer
                ->createCustomer(
                    (new CustomerTransfer())
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
                        )
                );

            $this
                ->document
                ->createInvoice($customerId);

            return true;
        } catch (TooManyRequestException $exception) {
            return false;
        }
    }
}
