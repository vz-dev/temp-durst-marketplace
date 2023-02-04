<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Acceptance\Yves\Customer\Presentation;

use PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerAddressesPage;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerNewsletterPage;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerOrdersPage;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerOverviewPage;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerProfilePage;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Yves
 * @group Customer
 * @group Presentation
 * @group CustomerOverviewCest
 * Add your own group annotations below this line
 */
class CustomerOverviewCest
{
    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testICanOpenOverviewPage(CustomerPresentationTester $i)
    {
        $i->amLoggedInCustomer();
        $i->amOnPage(CustomerOverviewPage::URL);

        $i->see(CustomerOverviewPage::BOX_HEADLINE_ORDERS, 'h5');
        $i->see(CustomerOverviewPage::BOX_HEADLINE_PROFILE, 'h5');
        $i->see(CustomerOverviewPage::BOX_HEADLINE_NEWSLETTER, 'h5');
        $i->see(CustomerOverviewPage::BOX_HEADLINE_SHIPPING_ADDRESS, 'h5');
        $i->see(CustomerOverviewPage::BOX_HEADLINE_BILLING_ADDRESS, 'h5');
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testCustomerWithoutAddressShouldSeeAddAddressInfoText(CustomerPresentationTester $i)
    {
        $i->amLoggedInCustomer();
        $i->amOnPage(CustomerOverviewPage::URL);

        $i->see(CustomerOverviewPage::INFO_TEXT_ADD_SHIPPING_ADDRESS);
        $i->see(CustomerOverviewPage::INFO_TEXT_ADD_BILLING_ADDRESS);
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testICanGoFromOverviewToProfilePage(CustomerPresentationTester $i)
    {
        $i->amLoggedInCustomer();
        $i->amOnPage(CustomerOverviewPage::URL);
        $i->click(CustomerOverviewPage::LINK_TO_PROFILE_PAGE);
        $i->amOnPage(CustomerProfilePage::URL);
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testICanGoFromOverviewToAddressesPage(CustomerPresentationTester $i)
    {
        $i->amLoggedInCustomer();
        $i->amOnPage(CustomerOverviewPage::URL);
        $i->click(CustomerOverviewPage::LINK_TO_ADDRESSES_PAGE);
        $i->amOnPage(CustomerAddressesPage::URL);
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testICanGoFromOverviewToOrdersPage(CustomerPresentationTester $i)
    {
        $i->amLoggedInCustomer();
        $i->amOnPage(CustomerOverviewPage::URL);
        $i->click(CustomerOverviewPage::LINK_TO_ORDERS_PAGE);
        $i->amOnPage(CustomerOrdersPage::URL);
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testICanGoFromOverviewToNewsletterPage(CustomerPresentationTester $i)
    {
        $i->amLoggedInCustomer();
        $i->amOnPage(CustomerOverviewPage::URL);
        $i->click(CustomerOverviewPage::LINK_TO_NEWSLETTER_PAGE);
        $i->amOnPage(CustomerNewsletterPage::URL);
    }
}
