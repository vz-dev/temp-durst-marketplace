<?php
/**
 * Durst - project - CustomerHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.05.18
 * Time: 12:58
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OrderKeyRequestInterface as Request;
use stdClass;

class CustomerHydrator implements QuoteHydratorInterface
{
    const PERSIST_AS_GUEST = true;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \stdClass $requestObject
     *
     * @return mixed|void
     */
    public function hydrateQuote(QuoteTransfer $quoteTransfer, stdClass $requestObject)
    {
        $quoteTransfer->setCustomer($this->hydrateCustomer($requestObject->{Request::KEY_CUSTOMER}));
    }

    /**
     * @param $customer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function hydrateCustomer($customer) : CustomerTransfer
    {
        $customerTransfer = (new CustomerTransfer())
            ->setSalutation(trim($customer->{Request::KEY_CUSTOMER_SALUTATION}))
            ->setFirstName(trim($customer->{Request::KEY_CUSTOMER_FIRST_NAME}))
            ->setLastName(trim($customer->{Request::KEY_CUSTOMER_LAST_NAME}))
            ->setEmail(trim($customer->{Request::KEY_CUSTOMER_EMAIL}))
            ->setCompany(trim($customer->{Request::KEY_CUSTOMER_COMPANY}))
            ->setPhone(trim($customer->{Request::KEY_CUSTOMER_PHONE}))
            ->setIsGuest(self::PERSIST_AS_GUEST);

        if (property_exists($customer, Request::KEY_CUSTOMER_ID) === true) {
            $customerTransfer->setHeidelpayRestId($customer->{Request::KEY_CUSTOMER_ID});
        }
        if (property_exists($customer, Request::KEY_CUSTOMER_PRIVATE) === true) {
            $customerTransfer->setIsPrivate(filter_var($customer->{Request::KEY_CUSTOMER_PRIVATE}, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }

        return $customerTransfer;
    }
}
