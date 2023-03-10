<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Acceptance\Yves\Customer\Presentation;

use PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerLoginPage;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerOverviewPage;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerPasswordForgottenPage;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Yves
 * @group Customer
 * @group Presentation
 * @group CustomerLoginCest
 * Add your own group annotations below this line
 */
class CustomerLoginCest
{
    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testICanOpenLoginPage(CustomerPresentationTester $i)
    {
        $i->amOnPage(CustomerLoginPage::URL);
        $i->see(CustomerLoginPage::TITLE_LOGIN, 'h4');
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testICanOpenForgotPasswordPage(CustomerPresentationTester $i)
    {
        $i->amOnPage(CustomerLoginPage::URL);
        $i->click(CustomerLoginPage::FORGOT_PASSWORD_LINK);
        $i->seeCurrentUrlEquals(CustomerPasswordForgottenPage::URL);
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testICanLoginWithValidData(CustomerPresentationTester $i)
    {
        $i->amOnPage(CustomerLoginPage::URL);
        $customerTransfer = $i->haveRegisteredCustomer();
        $i->submitLoginForm($customerTransfer->getEmail(), $customerTransfer->getPassword());
        $i->seeCurrentUrlEquals(CustomerOverviewPage::URL);
    }
}
