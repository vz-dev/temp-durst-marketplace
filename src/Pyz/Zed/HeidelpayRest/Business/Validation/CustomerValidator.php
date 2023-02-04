<?php
/**
 * Durst - project - CustomerValidator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 28.01.20
 * Time: 14:43
 */

namespace Pyz\Zed\HeidelpayRest\Business\Validation;

use Generated\Shared\Transfer\OrderTransfer;
use heidelpayPHP\Exceptions\HeidelpayApiException;
use heidelpayPHP\Resources\Customer;
use heidelpayPHP\Resources\EmbeddedResources\Address;
use Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface;
use Pyz\Zed\HeidelpayRest\HeidelpayRestConfig;

class CustomerValidator implements CustomerValidatorInterface
{
    public const B2B_REGISTERED_REGISTRATIONTYPE = 'registered';
    public const B2B_NOT_REGISTERED_REGISTRATIONTYPE = 'not_registered';

    public const ALLOWED_BUSINESS_TYPES = [
        self::B2B_REGISTERED_REGISTRATIONTYPE,
        self::B2B_NOT_REGISTERED_REGISTRATIONTYPE,
    ];

    /**
     * @var \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface
     */
    protected $clientWrapper;

    /**
     * @var \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig
     */
    protected $config;

    /**
     * @var HeidelpayRestToSalesBridgeInterface
     */
    protected $salesFacade;

    /**
     * CustomerValidator constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface $clientWrapper
     * @param \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig $config
     * @param \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface $salesFacade
     */
    public function __construct(
        ClientWrapperInterface $clientWrapper,
        HeidelpayRestConfig $config,
        HeidelpayRestToSalesBridgeInterface $salesFacade
    ) {
        $this->clientWrapper = $clientWrapper;
        $this->config = $config;
        $this->salesFacade = $salesFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCustomerValid(OrderTransfer $orderTransfer): bool
    {
        if ($orderTransfer->getHeidelpayRestCustomerId() === null || $orderTransfer->getIsPrivate() === null) {
            return false;
        }

        if ($orderTransfer->getIsHeidelpayCustomerRequested() === true) {
            return $orderTransfer
                ->getIsHeidelpayCustomerValid();
        }

        $customer = $this->fetchCustomer($orderTransfer);

        if ($orderTransfer->getIsPrivate() === true) {
            if ($this->validateB2CCustomer($customer) === false) {
                $this
                    ->updateHeidelpayCustomerState(
                        $orderTransfer,
                        false
                    );

                return false;
            }
        } else {
            if ($this->validateB2BCustomer($customer) === false) {
                $this
                    ->updateHeidelpayCustomerState(
                        $orderTransfer,
                        false
                    );

                return false;
            }
        }

        $validAddress = $this
            ->validateAddress($customer->getBillingAddress());

        $this
            ->updateHeidelpayCustomerState(
                $orderTransfer,
                $validAddress
            );

        return $validAddress;
    }

    /**
     * @param \heidelpayPHP\Resources\Customer|null $customer
     *
     * @return bool
     */
    protected function validateB2CCustomer(?Customer $customer): bool
    {
        if ($customer === null ||
            $customer->getEmail() === null ||
            $customer->getBirthDate() === null ||
            $customer->getFirstname() === null ||
            $customer->getLastname() === null) {
            return false;
        }

        return true;
    }

    /**
     * @param \heidelpayPHP\Resources\Customer|null $customer
     *
     * @return bool
     */
    protected function validateB2BCustomer(?Customer $customer): bool
    {
        if ($customer !== null &&
            $customer->getCompany() !== null &&
            $customer->getCompanyInfo() !== null &&
            in_array($customer->getCompanyInfo()->getRegistrationType(), self::ALLOWED_BUSINESS_TYPES) === true
        ) {
            if ($customer->getCompanyInfo()->getRegistrationType() === self::B2B_REGISTERED_REGISTRATIONTYPE) {
                if ($customer->getCompanyInfo()->getCommercialRegisterNumber() === null) {
                    return false;
                }
            } elseif ($customer->getCompanyInfo()->getRegistrationType() === self::B2B_NOT_REGISTERED_REGISTRATIONTYPE) {
                if ($customer->getEmail() === null ||
                    $customer->getBirthDate() === null ||
                    $customer->getCompanyInfo()->getFunction() === null ||
                    $customer->getCompanyInfo()->getCommercialSector() === null) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param \heidelpayPHP\Resources\EmbeddedResources\Address|null $address
     *
     * @return bool
     */
    protected function validateAddress(?Address $address): bool
    {
        if ($address === null ||
            $address->getCountry() === null ||
            $address->getCity() === null ||
            $address->getStreet() === null ||
            $address->getZip() === null) {
            return false;
        }

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \heidelpayPHP\Resources\Customer|null
     */
    protected function fetchCustomer(OrderTransfer $orderTransfer): ?Customer
    {
        try {
            return $this
                ->clientWrapper
                ->getHeidelpayClient($orderTransfer)
                ->fetchCustomer($orderTransfer->getHeidelpayRestCustomerId());
        } catch (HeidelpayApiException $e) {
            // if the api returns an error this exception is thrown.
            // we assume the customer is not valid
            return null;
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param bool $state
     * @return bool
     */
    protected function updateHeidelpayCustomerState(
        OrderTransfer $orderTransfer,
        bool $state
    ): bool
    {
        return $this
            ->salesFacade
            ->updateHeidelpayCustomerState(
                $orderTransfer
                    ->getIdSalesOrder(),
                $state
            );
    }
}
