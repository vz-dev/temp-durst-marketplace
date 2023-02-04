<?php
/**
 * Durst - project - Customer.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.01.20
 * Time: 10:32
 */

namespace Pyz\Zed\Easybill\Business\Resource;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\EasybillCustomerTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Pyz\Shared\Easybill\EasybillConstants;
use Pyz\Shared\HttpRequest\HttpRequestConstants;
use Pyz\Zed\Easybill\Business\Exception\EasybillException;

class Customer extends AbstractResource implements CustomerInterface
{
    protected const URL = '/customer';

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return int
     */
    public function createCustomer(CustomerTransfer $customerTransfer): int
    {
        $this->checkCustomerTransferRequirements($customerTransfer);
        $requestTransfer = $this
            ->createHttpRequestTransfer(
                $this->createEasybillCustomerTransfer($customerTransfer)->toArray(true),
                HttpRequestConstants::HTTP_VERB_POST,
                $this->getCreateUrl()
            );

        $responseTransfer = $this
            ->httpRequestService
            ->sendRequest($requestTransfer);

        $this->checkResponseCode($responseTransfer);
        $body = json_decode($responseTransfer->getBody(), true);
        $this->checkBody($body);

        return $body[static::KEY_ID];
    }

    /**
     * @param \Generated\Shared\Transfer\HttpResponseTransfer $httpResponseTransfer
     *
     * @return void
     */
    protected function checkResponseCode(HttpResponseTransfer $httpResponseTransfer): void
    {
        parent::checkResponseCode($httpResponseTransfer);
        if ($httpResponseTransfer->getCode() === EasybillConstants::CODE_INVALID_CUSTOMER) {
            throw EasybillException::invalidCustomer();
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Generated\Shared\Transfer\EasybillCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\EasybillCustomerTransfer
     */
    protected function createEasybillCustomerTransfer(CustomerTransfer $customerTransfer): EasybillCustomerTransfer
    {
        if ($customerTransfer->getBillingAddress()->count() < 1) {
            throw EasybillException::noBillingAddressInCustomer();
        }
        /** @var \Generated\Shared\Transfer\AddressTransfer $billingAddress */
        $billingAddress = $customerTransfer->getBillingAddress()->offsetGet(0);
        $this->checkAddressTransferRequirements($billingAddress);
        return (new EasybillCustomerTransfer())
            ->setFirstName($customerTransfer->getFirstName())
            ->setLastName($customerTransfer->getLastName())
            ->setCompanyName($customerTransfer->getCompany())
            ->setCountry($billingAddress->getIso2Code())
            ->setEmails([
                $customerTransfer->getEmail(),
            ])
            ->setPhone1($customerTransfer->getPhone())
            ->setStreet($billingAddress->getAddress1())
            ->setZipCode($billingAddress->getZipCode());
    }

    /**
     * @return string
     */
    protected function getCreateUrl(): string
    {
        return $this->config->getEasybillApiUrl(static::URL);
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    protected function checkCustomerTransferRequirements(CustomerTransfer $customerTransfer): void
    {
        $customerTransfer
            ->requireFirstName()
            ->requireLastName()
            ->requireCompany()
            ->requireEmail()
            ->requirePhone();
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return void
     */
    protected function checkAddressTransferRequirements(AddressTransfer $addressTransfer): void
    {
        $addressTransfer
            ->requireIso2Code()
            ->requireAddress1()
            ->requireZipCode();
    }
}
