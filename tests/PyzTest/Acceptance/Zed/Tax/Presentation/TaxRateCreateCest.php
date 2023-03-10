<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Acceptance\Zed\Tax\Presentation;

use PyzTest\Acceptance\Zed\Tax\PageObject\TaxRateCreatePage;
use PyzTest\Acceptance\Zed\Tax\PageObject\TaxRateListPage;
use PyzTest\Acceptance\Zed\Tax\TaxPresentationTester;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group Tax
 * @group Presentation
 * @group TaxRateCreateCest
 * Add your own group annotations below this line
 */
class TaxRateCreateCest
{
    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Zed\Tax\TaxPresentationTester $i
     *
     * @return void
     */
    public function testCreateValidTaxRateShouldShowSuccessMessage(TaxPresentationTester $i)
    {
        $i->wantTo('Create valid tax rate.');
        $i->expect('Tax rate is successfully created');

        $i->createTaxRate(TaxRateCreatePage::TAX_RATE_VALID);
        $i->seeMatches(TaxRateCreatePage::MESSAGE_SUCCESSFUL_ALERT_CREATION, 'div.alert-success');

        $i->removeTaxRateFromDatabase(TaxRateCreatePage::TAX_RATE_VALID);
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Zed\Tax\TaxPresentationTester $i
     *
     * @return void
     */
    public function testCreateInvalidTaxRateShouldShowErrorMessages(TaxPresentationTester $i)
    {
        $i->wantTo('Create invalid tax rate');
        $i->expect('Error messages are displayed. Tax rate is not created');

        $i->createTaxRate(TaxRateCreatePage::TAX_RATE_INVALID);
        $i->seeErrorMessages();
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Zed\Tax\TaxPresentationTester $i
     *
     * @return void
     */
    public function testBackToListOfTaxRatesShouldOpenTaxRateListPageWithoutSaving(TaxPresentationTester $i)
    {
        $i->wantTo('Create valid tax rate and back to list of tax rates');
        $i->expect('List of tax rates is opened, tax rate is not created');

        $i->createTaxRateWithoutSaving(TaxRateCreatePage::TAX_RATE_VALID_NOT_CREATED);
        $i->click(TaxRateCreatePage::SELECTOR_LIST_OF_TAX_RATES_BUTTON);

        $i->dontSeeMatches(TaxRateCreatePage::MESSAGE_SUCCESSFUL_ALERT_CREATION, 'div.alert-success');

        $i->searchForTaxRate(TaxRateCreatePage::TAX_RATE_VALID_NOT_CREATED);

        $i->waitForText(TaxRateListPage::MESSAGE_EMPTY_TABLE, 10);
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Zed\Tax\TaxPresentationTester $i
     *
     * @return void
     */
    public function testCreateTaxRateWhichAlreadyExistsShouldShowErrorMessage(TaxPresentationTester $i)
    {
        $i->wantTo('Create tax rate which already exists');
        $i->expect('Error message is displayed on attempt to create one and the same Tax Rate');

        $i->createTaxRate(TaxRateCreatePage::TAX_RATE_VALID);

        $i->wait(2);

        $i->createTaxRate(TaxRateCreatePage::TAX_RATE_VALID);
        $i->see(TaxRateCreatePage::ERROR_MESSAGE_TAX_RATE_ALREADY_EXISTS);
    }

    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Zed\Tax\TaxPresentationTester $i
     *
     * @return void
     */
    public function testCreateAlreadyExistedTaxRateShouldShowErrorMessage(TaxPresentationTester $i)
    {
        $i->wantTo('Create tax rate which already exists');
        $i->expect('Error message is displayed on attempt to create one and the same Tax Rate');

        $i->createOneAndTheSameTaxRate(TaxRateCreatePage::TAX_RATE_VALID);

        $i->wait(2);

        $i->see(TaxRateCreatePage::ERROR_MESSAGE_TAX_RATE_ALREADY_EXISTS);
    }
}
