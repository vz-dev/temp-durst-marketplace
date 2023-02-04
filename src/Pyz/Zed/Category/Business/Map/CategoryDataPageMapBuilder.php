<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 12.11.18
 * Time: 10:48
 */

namespace Pyz\Zed\Category\Business\Map;


use Generated\Shared\Search\ProductCategoryIndexMap;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Pyz\Shared\Category\CategoryConstants;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

class CategoryDataPageMapBuilder
{

    /**
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $categoryData
     * @param LocaleTransfer $localeTransfer
     * @return PageMapTransfer
     */
    public function buildPageMap(
        PageMapBuilderInterface $pageMapBuilder,
        array $categoryData,
        LocaleTransfer $localeTransfer
    ) : PageMapTransfer
    {
        $pageTransfer = (new PageMapTransfer())
            ->setStore(Store::getInstance()->getStoreName())
            ->setLocale($localeTransfer->getLocaleName())
            ->setType(CategoryConstants::CATEGORY_SEARCH_TYPE);

        $pageMapBuilder
            ->addSearchResultData($pageTransfer, ProductCategoryIndexMap::NAME, $categoryData[ProductCategoryIndexMap::NAME])
            ->addSearchResultData($pageTransfer, ProductCategoryIndexMap::COLOR_CODE, $categoryData[ProductCategoryIndexMap::COLOR_CODE])
            ->addSearchResultData($pageTransfer, ProductCategoryIndexMap::ID_PRODUCT_CATEGORY, $categoryData[ProductCategoryIndexMap::ID_PRODUCT_CATEGORY])
            ->addSearchResultData($pageTransfer, ProductCategoryIndexMap::IMAGE_URL, $categoryData[ProductCategoryIndexMap::IMAGE_URL])
            ->addSearchResultData($pageTransfer, ProductCategoryIndexMap::PRIORITY, $categoryData[ProductCategoryIndexMap::PRIORITY]);

        return $pageTransfer;
    }
}