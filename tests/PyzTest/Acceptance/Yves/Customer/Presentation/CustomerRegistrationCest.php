<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Acceptance\Yves\Customer\Presentation;

use PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerOverviewPage;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerRegistrationPage;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Yves
 * @group Customer
 * @group Presentation
 * @group CustomerRegistrationCest
 * Add your own group annotations below this line
 */
class CustomerRegistrationCest
{
    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testICanOpenRegistrationPage(CustomerPresentationTester $i)
    {
        $i->amOnPage(CustomerRegistrationPage::URL);
        $i->see(CustomerRegistrationPage::TITLE_CREATE_ACCOUNT, 'h4');
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Customer\CustomerPresentationTester $i
     *
     * @return void
     */
    public function testICanRegisterWithValidData(CustomerPresentationTester $i)
    {
        $i->amOnPage(CustomerRegistrationPage::URL);
        $i->fillOutRegistrationForm();
        $i->click(CustomerRegistrationPage::BUTTON_REGISTER);
        $i->seeCurrentUrlEquals(CustomerOverviewPage::URL);
        $i->see(CustomerRegistrationPage::SUCCESS_MESSAGE);
    }
}
