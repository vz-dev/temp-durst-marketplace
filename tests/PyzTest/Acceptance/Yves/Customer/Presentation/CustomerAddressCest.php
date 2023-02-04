<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Acceptance\Yves\Customer\Presentation;

use PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerAddressesPage;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerAddressPage;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Yves
 * @group Customer
 * @group Presentation
 * @group CustomerAddressCest
 * Add your own group annotations below this line
 */
class CustomerAddressCest
{
    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testICanAddNewAddress(CustomerPresentationTester $i)
    {
        $i->amLoggedInCustomer();
        $i->amOnPage(CustomerAddressPage::URL);

        $addressTransfer = CustomerAddressesPage::getAddressData(CustomerAddressesPage::ADDRESS_A);

        $i->selectOption(CustomerAddressPage::FORM_FIELD_SELECTOR_SALUTATION, $addressTransfer->getSalutation());
        $i->fillField(CustomerAddressPage::FORM_FIELD_SELECTOR_FIRST_NAME, $addressTransfer->getFirstName());
        $i->fillField(CustomerAddressPage::FORM_FIELD_SELECTOR_LAST_NAME, $addressTransfer->getLastName());
        $i->fillField(CustomerAddressPage::FORM_FIELD_SELECTOR_COMPANY, $addressTransfer->getCompany());
        $i->fillField(CustomerAddressPage::FORM_FIELD_SELECTOR_PHONE, $addressTransfer->getPhone());
        $i->fillField(CustomerAddressPage::FORM_FIELD_SELECTOR_STREET, $addressTransfer->getAddress1());
        $i->fillField(CustomerAddressPage::FORM_FIELD_SELECTOR_NUMBER, $addressTransfer->getAddress2());
        $i->fillField(CustomerAddressPage::FORM_FIELD_SELECTOR_ADDITION_TO_ADDRESS, $addressTransfer->getAddress3());
        $i->fillField(CustomerAddressPage::FORM_FIELD_SELECTOR_CITY, $addressTransfer->getCity());
        $i->fillField(CustomerAddressPage::FORM_FIELD_SELECTOR_ZIP_CODE, $addressTransfer->getZipCode());
        $i->selectOption(CustomerAddressPage::FORM_FIELD_SELECTOR_COUNTRY, $addressTransfer->getIso2Code());

        $i->click(CustomerAddressPage::BUTTON_SUBMIT);
        $i->waitForText(CustomerAddressPage::SUCCESS_MESSAGE);
    }
}
