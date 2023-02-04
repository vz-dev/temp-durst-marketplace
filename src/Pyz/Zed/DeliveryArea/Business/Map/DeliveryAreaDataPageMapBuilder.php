<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 08.11.18
 * Time: 09:45
 */

namespace Pyz\Zed\DeliveryArea\Business\Map;


use Generated\Shared\Search\DeliveryAreaIndexMap;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

class DeliveryAreaDataPageMapBuilder
{

    /**
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $deliveryareaData
     * @param LocaleTransfer $localeTransfer
     * @return PageMapTransfer
     */
    public function buildPageMap(
        PageMapBuilderInterface $pageMapBuilder,
        array $deliveryareaData,
        LocaleTransfer $localeTransfer
    ) : PageMapTransfer
    {
        $branches = [];
        $products =  [];
        $timeslots =  [];

        if (is_string($deliveryareaData[DeliveryAreaIndexMap::BRANCH_IDS])) {
            $branches = array_map('intval' , explode(',', $deliveryareaData[DeliveryAreaIndexMap::BRANCH_IDS]));
        }

        if (is_string($deliveryareaData[DeliveryAreaIndexMap::PRODUCT_IDS])) {
            $products = array_map('intval', explode(',', $deliveryareaData[DeliveryAreaIndexMap::PRODUCT_IDS]));
        }

        if (is_string($deliveryareaData[DeliveryAreaIndexMap::TIME_SLOT_IDS])) {
            $timeslots = array_map('intval', explode(',', $deliveryareaData[DeliveryAreaIndexMap::TIME_SLOT_IDS]));
        }

        $deliveryareaData[DeliveryAreaIndexMap::BRANCH_IDS] = $branches;
        $deliveryareaData[DeliveryAreaIndexMap::PRODUCT_IDS] = $products;
        $deliveryareaData[DeliveryAreaIndexMap::TIME_SLOT_IDS] = $timeslots;


        $pageMapTransfer = (new PageMapTransfer())
            ->setStore(Store::getInstance()->getStoreName())
            ->setLocale($localeTransfer->getLocaleName())
            ->setType(DeliveryAreaConstants::DELIVERY_AREA_SEARCH_TYPE);

        $pageMapBuilder
            ->addSearchResultData($pageMapTransfer, DeliveryAreaIndexMap::ID_DELIVERY_AREA, $deliveryareaData[DeliveryAreaIndexMap::ID_DELIVERY_AREA])
            ->addSearchResultData($pageMapTransfer, DeliveryAreaIndexMap::ZIP_CODE, $deliveryareaData[DeliveryAreaIndexMap::ZIP_CODE])
            ->addSearchResultData($pageMapTransfer, DeliveryAreaIndexMap::BRANCH_IDS, $deliveryareaData[DeliveryAreaIndexMap::BRANCH_IDS])
            ->addSearchResultData($pageMapTransfer, DeliveryAreaIndexMap::PRODUCT_IDS, $deliveryareaData[DeliveryAreaIndexMap::PRODUCT_IDS])
            ->addSearchResultData($pageMapTransfer, DeliveryAreaIndexMap::TIME_SLOT_IDS, $deliveryareaData[DeliveryAreaIndexMap::TIME_SLOT_IDS]);

        return $pageMapTransfer;
    }
}