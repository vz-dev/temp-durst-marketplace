<?php
/**
 * Durst - project - AddressHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.05.18
 * Time: 13:04
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Order\QuoteHydrator;


use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Yves\AppRestApi\Handler\Json\Request\OrderKeyRequestInterface as Request;

class AddressHydrator implements QuoteHydratorInterface
{
    const COUNTRY_ISO2_CODE = 'DE';

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param \stdClass $requestObject
     */
    public function hydrateQuote(QuoteTransfer $quoteTransfer, \stdClass $requestObject)
    {

        $quoteTransfer->setBillingAddress($this->hydrateAddressTransfer($requestObject->{Request::KEY_BILLING_ADDRESS}));
        $quoteTransfer->setShippingAddress($this->hydrateShippingAddress($requestObject->{Request::KEY_SHIPPING_ADDRESS}));
    }

    /**
     * @param $address
     * @return AddressTransfer
     */
    protected function hydrateAddressTransfer($address) : AddressTransfer
    {
        $addressTransfer = (new AddressTransfer())
            ->setFirstName(trim($address->{Request::KEY_ADDRESS_FIRST_NAME}))
            ->setLastName(trim($address->{Request::KEY_ADDRESS_LAST_NAME}))
            ->setSalutation(trim($address->{Request::KEY_ADDRESS_SALUTATION}))
            ->setAddress1(trim($address->{Request::KEY_ADDRESS_ADDRESS_1}))
            ->setAddress2(trim($address->{Request::KEY_ADDRESS_ADDRESS_2}))
            ->setAddress3(trim($address->{Request::KEY_ADDRESS_ADDRESS_3}))
            ->setZipCode(trim($address->{Request::KEY_ADDRESS_ZIP_CODE}))
            ->setCity(trim($address->{Request::KEY_ADDRESS_CITY}))
            ->setCompany(trim($address->{Request::KEY_ADDRESS_COMPANY}))
            ->setIso2Code(self::COUNTRY_ISO2_CODE)
            ->setPhone(trim($address->{Request::KEY_ADDRESS_PHONE}));

        if(isset($address->{Request::KEY_ADDRESS_COMMENT})){
            $addressTransfer->setComment(trim($address->{Request::KEY_ADDRESS_COMMENT}));
        }

        return $addressTransfer;
    }

    /**
     * @param \StdClass $address
     * @return AddressTransfer
     */
    protected function hydrateShippingAddress(\StdClass $address) : AddressTransfer
    {
        $addressTransfer = $this->hydrateAddressTransfer($address);

        if(property_exists($address, Request::KEY_ADDRESS_LAT) === true){
            $addressTransfer->setLat($address->{Request::KEY_ADDRESS_LAT});
        }
        if(property_exists($address, Request::KEY_ADDRESS_LNG) === true){
            $addressTransfer->setLng($address->{Request::KEY_ADDRESS_LNG});
        }
        if(property_exists($address, Request::KEY_ADDRESS_FLOOR) === true){
            $addressTransfer->setFloor($address->{Request::KEY_ADDRESS_FLOOR});
        }
        if(property_exists($address, Request::KEY_ADDRESS_ELEVATOR) === true){
            $addressTransfer->setElevator($address->{Request::KEY_ADDRESS_ELEVATOR});
        }

        return $addressTransfer;
    }
}
