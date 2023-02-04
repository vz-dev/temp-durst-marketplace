<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Category\Business;

use Generated\Shared\Transfer\CategoryTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Spryker\Zed\Category\Business\CategoryFacadeInterface as SprykerCategoryFacadeInterface;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

interface CategoryFacadeInterface extends SprykerCategoryFacadeInterface
{
    /**
     * @param \Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface $pageMapBuilder
     * @param array $cmsData
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     *
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function buildPageMap(PageMapBuilderInterface $pageMapBuilder, array $cmsData, LocaleTransfer $locale);

    /**
     * @param int $idLocale
     * @return array|CategoryTransfer[]
     */
    public function getCategoryList(int $idLocale) : array;

    /**
     * Build a page map for transferring Propel entity Category to JSON for Elasticsearch
     *
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $categoryData
     * @param LocaleTransfer $localeTransfer
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function buildCategoryPageMap(PageMapBuilderInterface $pageMapBuilder, array $categoryData, LocaleTransfer $localeTransfer) : PageMapTransfer;
}
