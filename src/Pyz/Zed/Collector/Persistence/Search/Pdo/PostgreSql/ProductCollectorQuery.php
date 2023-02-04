<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql;

use Generated\Shared\Search\ProductIndexMap;
use Pyz\Zed\ProductSearch\Business\Map\ProductDataPageMapBuilder;
use Spryker\Zed\Collector\Persistence\Collector\AbstractPdoCollectorQuery;

class ProductCollectorQuery extends AbstractPdoCollectorQuery
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
                   spy_product.id_product AS ' . ProductIndexMap::ID_PRODUCT . ',
                   spy_category.id_category AS ' . ProductIndexMap::ID_CATEGORY . ',
                   spy_product.sku AS ' . ProductIndexMap::SKU . ',
                   spy_product.attributes AS ' . ProductDataPageMapBuilder::KEY_ATTRIBUTES . ',
                   spy_deposit.deposit AS ' . ProductIndexMap::DEPOSIT . ',
                   spy_manufacturer.name AS ' . (str_replace('.', '_', ProductIndexMap::MANUFACTURER_NAME)) . ',
                   spy_manufacturer.address2 AS ' . (str_replace('.', '_', ProductIndexMap::MANUFACTURER_ADDRESS_1)) . ',
                   spy_manufacturer.address3 AS ' . (str_replace('.', '_', ProductIndexMap::MANUFACTURER_ADDRESS_2)) . '
            
            FROM spy_touch
            
                   LEFT JOIN spy_touch_search
                     ON ((spy_touch.id_touch = spy_touch_search.fk_touch AND spy_touch_search.fk_locale = :id_locale) AND spy_touch_search.fk_store = :id_store)
            
                   INNER JOIN spy_product
                     ON (spy_touch.item_id = spy_product.id_product)
            
                   INNER JOIN spy_product_abstract
                     ON (spy_product_abstract.id_product_abstract = spy_product.fk_product_abstract)
            
                   INNER JOIN spy_product_category
                     ON (spy_product_abstract.id_product_abstract = spy_product_category.fk_product_abstract)
            
                   INNER JOIN spy_category
                     ON (spy_product_category.fk_category = spy_category.id_category)
            
                   INNER JOIN spy_deposit
                     ON (spy_deposit.id_deposit = spy_product.fk_deposit)
            
                   INNER JOIN spy_manufacturer
                     ON (spy_product_abstract.fk_manufacturer = spy_manufacturer.id_manufacturer)
            
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
