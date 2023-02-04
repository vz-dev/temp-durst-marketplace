<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 07.11.18
 * Time: 10:10
 */

namespace Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql;


use Generated\Shared\Search\DeliveryAreaIndexMap;
use Spryker\Zed\Collector\Persistence\Collector\AbstractPdoCollectorQuery;

class DeliveryAreaCollectorQuery extends AbstractPdoCollectorQuery
{

    /**
     * @return void
     */
    protected function prepareQuery()
    {
        $sql = '
            SELECT
                   spy_touch.id_touch,
                   spy_touch.item_type,
                   spy_touch.item_event,
                   spy_touch.item_id,
                   spy_touch.touched,
                   spy_touch.id_touch AS collector_touch_id,
                   spy_touch.item_id AS collector_resource_id,
                   spy_touch_search.id_touch_search AS collector_search_key,
                   spy_delivery_area.id_delivery_area AS ' . DeliveryAreaIndexMap::ID_DELIVERY_AREA . ',
                   spy_delivery_area.zip_code AS ' . DeliveryAreaIndexMap::ZIP_CODE . ',
                   array_to_string(array_agg(DISTINCT spy_branch.id_branch), \',\') AS ' . DeliveryAreaIndexMap::BRANCH_IDS . ',
                   array_to_string(array_agg(DISTINCT spy_concrete_time_slot.id_concrete_time_slot), \',\') AS ' . DeliveryAreaIndexMap::TIME_SLOT_IDS . ',
                   array_to_string(array_agg(DISTINCT spy_product.id_product), \',\') AS ' . DeliveryAreaIndexMap::PRODUCT_IDS . '
            
            FROM spy_touch
            
                   LEFT JOIN spy_touch_search
                     ON ((spy_touch.id_touch = spy_touch_search.fk_touch AND spy_touch_search.fk_locale = :id_locale) AND spy_touch_search.fk_store = :id_store)
            
                   INNER JOIN spy_delivery_area
                     ON (spy_touch.item_id = spy_delivery_area.id_delivery_area)
            
                   INNER JOIN spy_time_slot
                     ON (spy_delivery_area.id_delivery_area = spy_time_slot.fk_delivery_area)
            
                   INNER JOIN spy_branch
                     ON (spy_time_slot.fk_branch = spy_branch.id_branch)
            
                   INNER JOIN spy_concrete_time_slot
                     ON (spy_time_slot.id_time_slot = spy_concrete_time_slot.fk_time_slot)
            
                   INNER JOIN merchant_price
                     ON (spy_branch.id_branch = merchant_price.fk_branch)
            
                   INNER JOIN spy_product
                     ON (merchant_price.fk_product = spy_product.id_product)
            
            WHERE
                spy_touch.item_type = :spy_touch_item_type
              AND
                spy_touch.item_event = :spy_touch_item_event
              AND
                spy_touch.touched >= :spy_touch_touched
        ';

        $this->criteriaBuilder
            ->sql($sql)
            ->setGroupBy(
                'spy_touch.id_touch,
                spy_delivery_area.id_delivery_area, 
                spy_touch_search.id_touch_search,
                spy_delivery_area.zip_code'
            )
            ->setParameter(':id_locale', $this->locale->getIdLocale())
            ->setParameter(':id_store', $this->storeTransfer->getIdStore());
    }
}