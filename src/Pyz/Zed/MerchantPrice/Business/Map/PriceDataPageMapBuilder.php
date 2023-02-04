<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 09.11.18
 * Time: 15:32
 */

namespace Pyz\Zed\MerchantPrice\Business\Map;


use Generated\Shared\Search\PriceIndexMap;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Pyz\Shared\MerchantPrice\MerchantPriceConstants;
use Pyz\Shared\ProductSearch\ProductSearchConfig;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

class PriceDataPageMapBuilder
{

    /**
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $priceData
     * @param LocaleTransfer $localeTransfer
     * @return PageMapTransfer
     */
    public function buildPageMap(
        PageMapBuilderInterface $pageMapBuilder,
        array $priceData,
        LocaleTransfer $localeTransfer
    ) : PageMapTransfer
    {
        $pageMapTransfer = (new PageMapTransfer())
            ->setStore(Store::getInstance()->getStoreName())
            ->setLocale($localeTransfer->getLocaleName())
            ->setType(MerchantPriceConstants::PRICE_SEARCH_TYPE);

        //  @see: Pyz\Zed\MerchantPrice\Business\Model\Catalog::calculateUnitPrice()
        if ($priceData[ProductSearchConfig::KEY_PRODUCT_SEARCH_BOTTLES] === null || $priceData[ProductSearchConfig::KEY_PRODUCT_SEARCH_VOLUME_PER_BOTTLE] === null) {
            $unitPrice = 0;
        } else {
            $sumVolume = $priceData[ProductSearchConfig::KEY_PRODUCT_SEARCH_BOTTLES] * $priceData[ProductSearchConfig::KEY_PRODUCT_SEARCH_VOLUME_PER_BOTTLE];

            if ($sumVolume === 0) {
                $unitPrice = 0;
            } else {
                $unitPrice = (int) round(($priceData[PriceIndexMap::PRICE] / ($sumVolume / 1000.0)));
            }
        }

        $priceData[PriceIndexMap::UNIT_PRICE] = $unitPrice;

        $pageMapBuilder
            ->addSearchResultData($pageMapTransfer, PriceIndexMap::ID_PRICE, $priceData[PriceIndexMap::ID_PRICE])
            ->addSearchResultData($pageMapTransfer, PriceIndexMap::ID_BRANCH, $priceData[PriceIndexMap::ID_BRANCH])
            ->addSearchResultData($pageMapTransfer, PriceIndexMap::CURRENCY, $priceData[PriceIndexMap::CURRENCY])
            ->addSearchResultData($pageMapTransfer, PriceIndexMap::ID_PRODUCT, $priceData[PriceIndexMap::ID_PRODUCT])
            ->addSearchResultData($pageMapTransfer, PriceIndexMap::PRICE, $priceData[PriceIndexMap::PRICE])
            ->addSearchResultData($pageMapTransfer, PriceIndexMap::UNIT_PRICE, $priceData[PriceIndexMap::UNIT_PRICE]);

        return $pageMapTransfer;
    }
}
