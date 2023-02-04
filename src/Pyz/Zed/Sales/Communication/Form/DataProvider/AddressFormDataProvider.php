<?php
/**
 * Durst - project - AddressFormDataProvider.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-01-13
 * Time: 19:23
 */

namespace Pyz\Zed\Sales\Communication\Form\DataProvider;

use Pyz\Zed\Sales\Communication\Form\AddressForm;
use Spryker\Zed\Sales\Communication\Form\DataProvider\AddressFormDataProvider as SprykerAddressFormDataProvider;

class AddressFormDataProvider extends SprykerAddressFormDataProvider
{
    /**
     * @param int $idOrderAddress
     *
     * @return array
     */
    public function getData($idOrderAddress)
    {
        $address = $this->salesQueryContainer->querySalesOrderAddressById($idOrderAddress)->findOne();

        return [
            AddressForm::FIELD_FIRST_NAME => $address->getFirstName(),
            AddressForm::FIELD_MIDDLE_NAME => $address->getMiddleName(),
            AddressForm::FIELD_LAST_NAME => $address->getLastName(),
            AddressForm::FIELD_EMAIL => $address->getEmail(),
            AddressForm::FIELD_ADDRESS_1 => $address->getAddress1(),
            AddressForm::FIELD_ADDRESS_2 => $address->getAddress2(),
            AddressForm::FIELD_COMPANY => $address->getCompany(),
            AddressForm::FIELD_CITY => $address->getCity(),
            AddressForm::FIELD_ZIP_CODE => $address->getZipCode(),
            AddressForm::FIELD_PO_BOX => $address->getPoBox(),
            AddressForm::FIELD_PHONE => $address->getPhone(),
            AddressForm::FIELD_CELL_PHONE => $address->getCellPhone(),
            AddressForm::FIELD_DESCRIPTION => $address->getDescription(),
            AddressForm::FIELD_COMMENT => $address->getComment(),
            AddressForm::FIELD_SALUTATION => $address->getSalutation(),
            AddressForm::FIELD_FK_COUNTRY => $address->getFkCountry(),
            AddressForm::FIELD_LAT => $address->getLat(),
            AddressForm::FIELD_LNG => $address->getLng(),
            AddressForm::FIELD_FLOOR => $address->getFloor(),
            AddressForm::FIELD_ELEVATOR => $address->getElevator(),
        ];
    }
}
