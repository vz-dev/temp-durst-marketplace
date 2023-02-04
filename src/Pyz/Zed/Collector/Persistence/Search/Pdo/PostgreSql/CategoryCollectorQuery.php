<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 12.11.18
 * Time: 10:02
 */

namespace Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql;


use Generated\Shared\Search\ProductCategoryIndexMap;
use Spryker\Zed\Collector\Persistence\Collector\AbstractPdoCollectorQuery;

class CategoryCollectorQuery extends AbstractPdoCollectorQuery
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
                   spy_category.id_category AS ' . ProductCategoryIndexMap::ID_PRODUCT_CATEGORY . ',
                   spy_category_attribute.name AS ' . ProductCategoryIndexMap::NAME . ',
                   spy_category_attribute.image_url AS ' . ProductCategoryIndexMap::IMAGE_URL . ',
                   spy_category_attribute.color_code AS ' . ProductCategoryIndexMap::COLOR_CODE . ',
                   spy_category_attribute.priority AS ' . ProductCategoryIndexMap::PRIORITY . '
            
            FROM spy_touch
            
                   LEFT JOIN spy_touch_search
                     ON ((spy_touch.id_touch = spy_touch_search.fk_touch AND spy_touch_search.fk_locale = :id_locale) AND spy_touch_search.fk_store = :id_store)
            
                   INNER JOIN spy_category
                     ON (spy_touch.item_id = spy_category.id_category)
            
                   INNER JOIN spy_category_attribute
                     ON (spy_category_attribute.fk_category = spy_category.id_category)
            
            WHERE
                spy_touch.item_type = :spy_touch_item_type
              AND
                spy_touch.item_event = :spy_touch_item_event
              AND
                spy_touch.touched >= :spy_touch_touched
        ';

        $this->criteriaBuilder
            ->sql($sql)
            ->setParameter(':id_locale', $this->locale->getIdLocale())
            ->setParameter(':id_store', $this->storeTransfer->getIdStore());
    }
}