<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Category\Persistence;

use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Category\Persistence\SpyCategoryQuery;
use Spryker\Zed\Category\Persistence\CategoryQueryContainer as SprykerCategoryQueryContainer;

/**
 * @method \Spryker\Zed\Category\Persistence\CategoryPersistenceFactory getFactory()
 */
class CategoryQueryContainer extends SprykerCategoryQueryContainer implements CategoryQueryContainerInterface
{
    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Orm\Zed\Category\Persistence\SpyCategoryAttributeQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryCategoryAttributesByLocale(LocaleTransfer $localeTransfer)
    {
        return $this->getFactory()
            ->createCategoryAttributeQuery()
            ->filterByFkLocale($localeTransfer->getIdLocale());
    }

    /**
     * @return SpyCategoryQuery
     */
    public function queryAllCategories()
    {
        return SpyCategoryQuery::create();
    }
}
