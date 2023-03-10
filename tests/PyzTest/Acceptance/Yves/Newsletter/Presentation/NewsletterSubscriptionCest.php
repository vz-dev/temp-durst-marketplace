<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Acceptance\Yves\Newsletter\Presentation;

use Generated\Shared\DataBuilder\CustomerBuilder;
use PyzTest\Acceptance\Yves\Application\PageObject\Homepage;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerNewsletterPage;
use PyzTest\Acceptance\Yves\Customer\PageObject\CustomerOverviewPage;
use PyzTest\Acceptance\Yves\Newsletter\NewsletterPresentationTester;
use PyzTest\Acceptance\Yves\Newsletter\PageObject\NewsletterSubscriptionHomePage;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Yves
 * @group Newsletter
 * @group Presentation
 * @group NewsletterSubscriptionCest
 * Add your own group annotations below this line
 */
class NewsletterSubscriptionCest
{
    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Newsletter\NewsletterPresentationTester $i
     *
     * @return void
     */
    public function iCanSubscribeWithAnUnsubscribedEmail(NewsletterPresentationTester $i)
    {
        $i->wantTo('Subscribe to the newsletter with an unsubscribed new email.');
        $i->expect('Success message is displayed.');

        $i->amOnPage(Homepage::URL);

        $customerTransfer = $this->buildCustomerTransfer();

        $i->fillField(NewsletterSubscriptionHomePage::FORM_SELECTOR, $customerTransfer->getEmail());
        $i->click(NewsletterSubscriptionHomePage::FORM_SUBMIT);

        $i->see(NewsletterSubscriptionHomePage::SUCCESS_MESSAGE);
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Newsletter\NewsletterPresentationTester $i
     *
     * @return void
     */
    public function iCanNotSubscribeWithAnAlreadySubscribedEmail(NewsletterPresentationTester $i)
    {
        $i->wantTo('Subscribe to the newsletter with an already subscribed email.');
        $i->expect('Error message is displayed.');

        $i->amOnPage(Homepage::URL);

        $customerTransfer = $this->buildCustomerTransfer();

        $i->haveAnAlreadySubscribedEmail($customerTransfer->getEmail());

        $i->fillField(NewsletterSubscriptionHomePage::FORM_SELECTOR, $customerTransfer->getEmail());
        $i->click(NewsletterSubscriptionHomePage::FORM_SUBMIT);

        $i->see(NewsletterSubscriptionHomePage::ERROR_MESSAGE);
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Newsletter\NewsletterPresentationTester $i
     *
     * @return void
     */
    public function subscribedEmailIsLinkedWithCustomerAfterRegistration(NewsletterPresentationTester $i)
    {
        $i->wantTo('Subscribe to the newsletter with an unsubscribed email and later on register with that address.');
        $i->expect('Subscriber email should be linked with registered customer.');

        $i->amOnPage(Homepage::URL);

        $customerTransfer = $this->buildCustomerTransfer();

        $i->fillField(NewsletterSubscriptionHomePage::FORM_SELECTOR, $customerTransfer->getEmail());
        $i->click(NewsletterSubscriptionHomePage::FORM_SUBMIT);

        $i->amLoggedInCustomer($customerTransfer->toArray());

        $i->amOnPage(CustomerOverviewPage::URL);
        $i->see(CustomerOverviewPage::NEWSLETTER_SUBSCRIBED);
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Yves\Newsletter\NewsletterPresentationTester $i
     *
     * @return void
     */
    public function subscribedEmailCanBeUnsubscribedByCustomerAfterRegistration(NewsletterPresentationTester $i)
    {
        $i->wantTo('Subscribe to the newsletter with an unsubscribed email should be able to unsubscribe after registration.');
        $i->expect('Subscribed email should be unsubscribed after customer unsubscribe.');

        $i->amOnPage(Homepage::URL);

        $customerTransfer = $this->buildCustomerTransfer();

        $i->fillField(NewsletterSubscriptionHomePage::FORM_SELECTOR, $customerTransfer->getEmail());
        $i->click(NewsletterSubscriptionHomePage::FORM_SUBMIT);

        $i->amLoggedInCustomer($customerTransfer->toArray());

        $i->amOnPage(CustomerOverviewPage::URL);
        $i->see(CustomerOverviewPage::NEWSLETTER_SUBSCRIBED);

        $i->amOnPage(CustomerNewsletterPage::URL);
        $i->amOnPage(CustomerNewsletterPage::URL);
        $i->click(['name' => CustomerNewsletterPage::FORM_FIELD_SELECTOR_NEWSLETTER_SUBSCRIPTION]);
        $i->click(CustomerNewsletterPage::BUTTON_SUBMIT);
        $i->waitForText(CustomerNewsletterPage::SUCCESS_MESSAGE_UN_SUBSCRIBED);

        $i->dontSeeCheckboxIsChecked(['name' => CustomerNewsletterPage::FORM_FIELD_SELECTOR_NEWSLETTER_SUBSCRIPTION]);
    }

    /**
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer|\Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function buildCustomerTransfer()
    {
        $customerTransfer = (new CustomerBuilder())->build();

        return $customerTransfer;
    }
}
