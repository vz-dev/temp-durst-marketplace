<?php
/**
 * Durst - project - ConcreteTimeSlotCollector.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 16.10.18
 * Time: 21:42
 */

namespace Pyz\Zed\Collector\Business\Storage;


use Orm\Zed\DeliveryArea\Persistence\Map\SpyConcreteTimeSlotTableMap;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyDeliveryAreaTableMap;
use Orm\Zed\DeliveryArea\Persistence\Map\SpyTimeSlotTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Spryker\Zed\Collector\Business\Collector\Storage\AbstractStoragePropelCollector;

class ConcreteTimeSlotCollector extends AbstractStoragePropelCollector
{
    public const KEY_ID_CONCRETE_TIME_SLOT = 'id_concrete_time_slot';
    public const KEY_START_TIME = 'start_time';
    public const KEY_END_TIME = 'end_time';
    public const KEY_DELIVERY_COSTS = 'delivery_costs';
    public const KEY_MAX_CUSTOMERS = 'max_customers';
    public const KEY_MAX_PRODUCTS = 'max_products';
    public const KEY_MIN_VALUE_FIRST = 'min_value_first';
    public const KEY_MIN_VALUE_FOLLOWING = 'min_value_following';
    public const KEY_MIN_UNITS = 'min_units';
    public const KEY_IS_ACTIVE = 'is_active';
    public const KEY_ZIP_CODE = 'zip_code';
    public const KEY_ID_BRANCH = 'id_branch';

    /**
     * {@inheritdoc}
     *
     * @param string $touchKey
     * @param array $collectItemData
     *
     * @return array
     */
    protected function collectItem(
        $touchKey,
        array $collectItemData
    )
    {
        return [
            static::KEY_ID_CONCRETE_TIME_SLOT => $collectItemData[static::KEY_ID_CONCRETE_TIME_SLOT],
            static::KEY_START_TIME => $collectItemData[static::KEY_START_TIME],
            static::KEY_END_TIME => $collectItemData[static::KEY_END_TIME],
            static::KEY_DELIVERY_COSTS => $collectItemData[static::KEY_DELIVERY_COSTS],
            static::KEY_MAX_CUSTOMERS => $collectItemData[static::KEY_MAX_CUSTOMERS],
            static::KEY_MAX_PRODUCTS => $collectItemData[static::KEY_MAX_PRODUCTS],
            static::KEY_MIN_VALUE_FIRST => $collectItemData[static::KEY_MIN_VALUE_FIRST],
            static::KEY_MIN_VALUE_FOLLOWING => $collectItemData[static::KEY_MIN_VALUE_FOLLOWING],
            static::KEY_MIN_UNITS => $collectItemData[static::KEY_MIN_UNITS],
            static::KEY_IS_ACTIVE => $collectItemData[static::KEY_IS_ACTIVE],
            static::KEY_ZIP_CODE => $collectItemData[static::KEY_ZIP_CODE],
            static::KEY_ID_BRANCH => $collectItemData[static::KEY_ID_BRANCH],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function collectResourceType()
    {
        return DeliveryAreaConstants::RESOURCE_TYPE_CONCRETE_TIME_SLOT;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $data
     * @param string $localeName
     * @param array $collectedItemData
     *
     * @return string
     */
    protected function collectKey($data, $localeName, array $collectedItemData)
    {
        return $this->generateKey($collectedItemData[static::KEY_ID_CONCRETE_TIME_SLOT], $localeName);
    }
}