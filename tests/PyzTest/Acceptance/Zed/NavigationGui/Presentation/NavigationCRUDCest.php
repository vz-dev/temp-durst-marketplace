<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Acceptance\Zed\NavigationGui\Presentation;

use PyzTest\Acceptance\Zed\NavigationGui\NavigationGuiPresentationTester;
use PyzTest\Acceptance\Zed\NavigationGui\PageObject\NavigationCreatePage;
use PyzTest\Acceptance\Zed\NavigationGui\PageObject\NavigationDeletePage;
use PyzTest\Acceptance\Zed\NavigationGui\PageObject\NavigationPage;
use PyzTest\Acceptance\Zed\NavigationGui\PageObject\NavigationStatusTogglePage;
use PyzTest\Acceptance\Zed\NavigationGui\PageObject\NavigationUpdatePage;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Zed
 * @group NavigationGui
 * @group Presentation
 * @group NavigationCRUDCest
 * Add your own group annotations below this line
 */
class NavigationCRUDCest
{
    /**
     * @skip
     *
     * @param \PyzTest\Acceptance\Zed\NavigationGui\NavigationGuiPresentationTester $i
     *
     * @return void
     */
    public function testICanCreateReadUpdateAndDeleteNavigation(NavigationGuiPresentationTester $i)
    {
        $i->amLoggedInUser();
        $i->amOnPage(NavigationCreatePage::URL);

        $idNavigation = $this->create($i);

        $this->read($i);

        $this->update($i, $idNavigation);

        $this->activate($i);

        $this->delete($i);
    }

    /**
     * @param \PyzTest\Acceptance\Zed\NavigationGui\NavigationGuiPresentationTester $i
     *
     * @return int
     */
    protected function create(NavigationGuiPresentationTester $i)
    {
        $i->wantTo('Create navigation.');
        $i->expect('Navigation is persisted in Zed.');

        $i->setNameField('Acceptance navigation (1)');
        $i->setKeyField('acceptance1');
        $i->checkIsActiveField(true);
        $i->submitNavigationForm();
        $i->seeCurrentUrlEquals(NavigationPage::URL);
        $idNavigation = $i->seeSuccessMessage(NavigationCreatePage::MESSAGE_SUCCESS);

        return $idNavigation;
    }

    /**
     * @param \PyzTest\Acceptance\Zed\NavigationGui\NavigationGuiPresentationTester $i
     *
     * @return void
     */
    protected function read(NavigationGuiPresentationTester $i)
    {
        $i->wantTo('See navigation list.');
        $i->expect('Navigation table is shown and not empty');

        $i->waitForElementVisible(NavigationPage::PAGE_LIST_TABLE_XPATH, 5);
    }

    /**
     * @param \PyzTest\Acceptance\Zed\NavigationGui\NavigationGuiPresentationTester $i
     * @param int $idNavigation
     *
     * @return void
     */
    protected function update(NavigationGuiPresentationTester $i, $idNavigation)
    {
        $i->wantTo('Update existing navigation.');
        $i->expect('Navigation is persisted in Zed');

        $i->amOnPage(sprintf(NavigationUpdatePage::URL, $idNavigation));
        $i->setNameField('Acceptance navigation (1) - edited');
        $i->checkIsActiveField(false);
        $i->submitNavigationForm();
        $i->seeCurrentUrlEquals(NavigationPage::URL);
        $i->seeSuccessMessage(NavigationUpdatePage::MESSAGE_SUCCESS);
    }

    /**
     * @param \PyzTest\Acceptance\Zed\NavigationGui\NavigationGuiPresentationTester $i
     *
     * @return void
     */
    protected function activate(NavigationGuiPresentationTester $i)
    {
        $i->wantTo('Activate navigation.');
        $i->expect('New navigation status persisted in Zed.');

        $i->amOnPage(NavigationPage::URL);
        $i->waitForElementVisible(NavigationPage::PAGE_LIST_TABLE_XPATH, 5);
        $i->activateFirstNavigationRow();
        $i->seeSuccessMessage(NavigationStatusTogglePage::MESSAGE_ACTIVE_SUCCESS);
        $i->seeCurrentUrlEquals(NavigationPage::URL);
    }

    /**
     * @param \PyzTest\Acceptance\Zed\NavigationGui\NavigationGuiPresentationTester $i
     *
     * @return void
     */
    protected function delete(NavigationGuiPresentationTester $i)
    {
        $i->wantTo('Delete navigation.');
        $i->expect('Navigation is removed from Zed.');

        $i->amOnPage(NavigationPage::URL);
        $i->waitForElementVisible(NavigationPage::PAGE_LIST_TABLE_XPATH, 5);
        $i->wait(1); // TODO: remove "wait" once flash messages show up consistently.
        $i->deleteFirstNavigationRow();
        $i->seeSuccessMessage(NavigationDeletePage::MESSAGE_SUCCESS);
        $i->seeCurrentUrlEquals(NavigationPage::URL);
    }
}
