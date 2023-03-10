<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Acceptance\Zed\CmsGui\Presentation;

use PyzTest\Acceptance\Zed\CmsGui\CmsGuiPresentationTester;
use PyzTest\Acceptance\Zed\CmsGui\PageObject\CmsCreateGlossaryPage;
use PyzTest\Acceptance\Zed\CmsGui\PageObject\CmsCreatePage;
use PyzTest\Acceptance\Zed\CmsGui\PageObject\CmsEditPage;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group CmsGui
 * @group Presentation
 * @group CmsGuiCreatePageCest
 * Add your own group annotations below this line
 */
class CmsGuiCreatePageCest
{
    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Zed\CmsGui\CmsGuiPresentationTester $i
     *
     * @return void
     */
    public function testICanCreateCmsPageWithTranslatedPlaceholders(CmsGuiPresentationTester $i)
    {
        $i->wantTo('Create cms page with multiple translations');
        $i->expect('Page is persisted in Zed, exported to Yves and is accesible.');

        $i->amLoggedInUser();
        $i->amOnPage(CmsCreatePage::URL);
        $i->selectOption('//*[@id="cms_page_fkTemplate"]', 'static full page');
        $i->setValidFrom('1985-07-01');
        $i->setValidTo('2050-07-01');
        $i->setIsSearchable();

        $i->fillLocalizedUrlForm(0, CmsCreatePage::getLocalizedName('en'), CmsCreatePage::getLocalizedUrl('en'));
        $i->expandLocalizedUrlPane();
        $i->fillLocalizedUrlForm(1, CmsCreatePage::getLocalizedName('de'), CmsCreatePage::getLocalizedUrl('de'));
        $i->clickSubmit();

        $i->see(CmsCreatePage::PAGE_CREATED_SUCCESS_MESSAGE);

        $i->includeJquery();

        $i->fillPlaceholderContents(0, 0, CmsCreateGlossaryPage::getLocalizedPlaceholderData('title', 'en'));
        $i->fillPlaceholderContents(0, 1, CmsCreateGlossaryPage::getLocalizedPlaceholderData('title', 'de'));

        $i->fillPlaceholderContents(1, 0, CmsCreateGlossaryPage::getLocalizedPlaceholderData('contents', 'en'));
        $i->fillPlaceholderContents(1, 1, CmsCreateGlossaryPage::getLocalizedPlaceholderData('contents', 'de'));

        $i->clickSubmit();

        $idCmsPage = $i->grabCmsPageId();

        $i->amOnPage(sprintf(CmsEditPage::URL, $idCmsPage));

        $i->clickPublishButton();

        $i->see(CmsEditPage::PAGE_PUBLISH_SUCCESS_MESSAGE);

        // TODO re-enable
//        $i->runCollectors();
//        $yvesTester = $i->haveFriend('yvesTester', YvesAcceptanceTester::class);
//
//        $yvesTester->does(function (YvesAcceptanceTester $i) {
//
//            $i->amOnPage(CmsCreatePage::getLocalizedUrl('de'));
//
//            $i->see(CmsCreateGlossaryPage::getLocalizedPlaceholderData('title', 'de'));
//            $i->see(CmsCreateGlossaryPage::getLocalizedPlaceholderData('contents', 'de'));
//
//            $i->amOnPage(CmsCreatePage::getLocalizedUrl('en'));
//
//            $i->see(CmsCreateGlossaryPage::getLocalizedPlaceholderData('title', 'en'));
//            $i->see(CmsCreateGlossaryPage::getLocalizedPlaceholderData('contents', 'en'));
//
//        });
    }
}
