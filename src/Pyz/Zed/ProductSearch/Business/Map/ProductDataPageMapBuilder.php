<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\ProductSearch\Business\Map;

use Generated\Shared\Search\ProductIndexMap;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Pyz\Shared\Product\ProductConstants;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

/**
 * @method \Pyz\Zed\Collector\Communication\CollectorCommunicationFactory getFactory()
 */
class ProductDataPageMapBuilder
{

    public const KEY_ATTRIBUTES = 'attributes';

    /**
     * @param \Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface $pageMapBuilder
     * @param array $productData
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function buildPageMap(PageMapBuilderInterface $pageMapBuilder, array $productData, LocaleTransfer $localeTransfer) : PageMapTransfer
    {
        $pageMapTransfer = (new PageMapTransfer())
            ->setStore(Store::getInstance()->getStoreName())
            ->setLocale($localeTransfer->getLocaleName())
            ->setType(ProductConstants::PRODUCT_SEARCH_TYPE);

        $productData = $this->handleAttributes($productData);

        $productData[ProductIndexMap::MANUFACTURER] = [
            ProductIndexMap::MANUFACTURER_NAME      => $productData[str_replace('.', '_', ProductIndexMap::MANUFACTURER_NAME)],
            ProductIndexMap::MANUFACTURER_ADDRESS_1 => $productData[str_replace('.', '_', ProductIndexMap::MANUFACTURER_ADDRESS_1)],
            ProductIndexMap::MANUFACTURER_ADDRESS_2 => $productData[str_replace('.', '_', ProductIndexMap::MANUFACTURER_ADDRESS_2)]
        ];

        $pageMapBuilder
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::NAME, $productData[ProductIndexMap::NAME])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::ID_PRODUCT, $productData[ProductIndexMap::ID_PRODUCT])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::ALCOHOL_BY_VOLUME, $productData[ProductIndexMap::ALCOHOL_BY_VOLUME])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::ALLERGENS, $productData[ProductIndexMap::ALLERGENS])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::DEPOSIT, $productData[ProductIndexMap::DEPOSIT])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::DESCRIPTION, $productData[ProductIndexMap::DESCRIPTION])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::ID_CATEGORY, $productData[ProductIndexMap::ID_CATEGORY])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::IMAGE_BOTTLE, $productData[ProductIndexMap::IMAGE_BOTTLE])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::IMAGE_BOTTLE_THUMB, $productData[ProductIndexMap::IMAGE_BOTTLE_THUMB])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::IMAGE_LIST, $productData[ProductIndexMap::IMAGE_LIST])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::INGREDIENTS, $productData[ProductIndexMap::INGREDIENTS])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::MANUFACTURER, $productData[ProductIndexMap::MANUFACTURER])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::NUTRITIONAL_VALUES, $productData[ProductIndexMap::NUTRITIONAL_VALUES])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::PRODUCT_LOGO, $productData[ProductIndexMap::PRODUCT_LOGO])
            ->addSearchResultData($pageMapTransfer, ProductIndexMap::SKU, $productData[ProductIndexMap::SKU]);

        return $pageMapTransfer;
    }

    /**
     * @param array $productData
     * @return array
     */
    protected function handleAttributes(array $productData) : array
    {
        $json = \json_decode($productData[self::KEY_ATTRIBUTES], true);

        $productData[ProductIndexMap::PRODUCT_LOGO] = '';
        $productData[ProductIndexMap::NUTRITIONAL_VALUES] = '';
        $productData[ProductIndexMap::INGREDIENTS] = '';
        $productData[ProductIndexMap::IMAGE_LIST] = '';
        $productData[ProductIndexMap::IMAGE_BOTTLE] = '';
        $productData[ProductIndexMap::IMAGE_BOTTLE_THUMB] = '';
        $productData[ProductIndexMap::DESCRIPTION] = '';
        $productData[ProductIndexMap::ALLERGENS] = '';
        $productData[ProductIndexMap::ALCOHOL_BY_VOLUME] = '';
        $productData[ProductIndexMap::NAME] = '';

        if (isset($json[ProductIndexMap::PRODUCT_LOGO])) {
            $productData[ProductIndexMap::PRODUCT_LOGO] = $json[ProductIndexMap::PRODUCT_LOGO];
        }

        if (isset($json[ProductIndexMap::NUTRITIONAL_VALUES])) {
            $productData[ProductIndexMap::NUTRITIONAL_VALUES] = $json[ProductIndexMap::NUTRITIONAL_VALUES];
        }

        if (isset($json[ProductIndexMap::INGREDIENTS])) {
            $productData[ProductIndexMap::INGREDIENTS] = $json[ProductIndexMap::INGREDIENTS];
        }

        if (isset($json[ProductIndexMap::IMAGE_LIST])) {
            $productData[ProductIndexMap::IMAGE_LIST] = $json[ProductIndexMap::IMAGE_LIST];
        }

        if (isset($json[ProductIndexMap::IMAGE_BOTTLE])) {
            $productData[ProductIndexMap::IMAGE_BOTTLE] = $json[ProductIndexMap::IMAGE_BOTTLE];
        }

        if (isset($json[ProductIndexMap::IMAGE_BOTTLE_THUMB])) {
            $productData[ProductIndexMap::IMAGE_BOTTLE_THUMB] = $json[ProductIndexMap::IMAGE_BOTTLE_THUMB];
        }

        if (isset($json[ProductIndexMap::DESCRIPTION])) {
            $productData[ProductIndexMap::DESCRIPTION] = $json[ProductIndexMap::DESCRIPTION];
        }

        if (isset($json[ProductIndexMap::ALLERGENS])) {
            $productData[ProductIndexMap::ALLERGENS] = $json[ProductIndexMap::ALLERGENS];
        }

        if (isset($json[ProductIndexMap::ALCOHOL_BY_VOLUME])) {
            $productData[ProductIndexMap::ALCOHOL_BY_VOLUME] = $json[ProductIndexMap::ALCOHOL_BY_VOLUME];
        }

        if (isset($json[ProductIndexMap::NAME])) {
            $productData[ProductIndexMap::NAME] = $json[ProductIndexMap::NAME];
        }

        return $productData;
    }
}
