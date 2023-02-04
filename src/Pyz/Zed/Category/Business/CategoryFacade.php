<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Category\Business;

use Generated\Shared\Transfer\CategoryTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Spryker\Zed\Category\Business\CategoryFacade as SprykerCategoryFacade;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

/**
 * @method \Pyz\Zed\Category\Business\CategoryBusinessFactory getFactory()
 */
class CategoryFacade extends SprykerCategoryFacade implements CategoryFacadeInterface
{
    /**
     * @param \Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface $pageMapBuilder
     * @param array $cmsData
     * @param \Generated\Shared\Transfer\LocaleTransfer $locale
     *
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function buildPageMap(PageMapBuilderInterface $pageMapBuilder, array $cmsData, LocaleTransfer $locale)
    {
        return $this
            ->getFactory()
            ->createCategoryNodeDataPageMapBuilder()
            ->buildPageMap($pageMapBuilder, $cmsData, $locale);
    }

    /**
     * @param int $idLocale
     * @return array|CategoryTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getCategoryList(int $idLocale): array
    {
        return $this
            ->getFactory()
            ->createCategoryList()
            ->getCategoryList($idLocale);
    }

    /**
     * {@inheritdoc}
     *
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $categoryData
     * @param LocaleTransfer $localeTransfer
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function buildCategoryPageMap(PageMapBuilderInterface $pageMapBuilder, array $categoryData, LocaleTransfer $localeTransfer) : PageMapTransfer
    {
        return $this
            ->getFactory()
            ->createCategoryDataPageMapBuilder()
            ->buildPageMap($pageMapBuilder, $categoryData, $localeTransfer);
    }
}
