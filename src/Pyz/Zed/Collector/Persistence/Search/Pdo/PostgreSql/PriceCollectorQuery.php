<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 09.11.18
 * Time: 14:39
 */

namespace Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql;


use Generated\Shared\Search\PriceIndexMap;
use Pyz\Shared\ProductSearch\ProductSearchConfig;
use Spryker\Zed\Collector\Persistence\Collector\AbstractPdoCollectorQuery;

class PriceCollectorQuery extends AbstractPdoCollectorQuery
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
                   merchant_price.id_price AS ' . PriceIndexMap::ID_PRICE . ',
                   merchant_price.fk_branch AS ' . PriceIndexMap::ID_BRANCH . ',
                   merchant_price.fk_product AS ' . PriceIndexMap::ID_PRODUCT . ',
                   merchant_price.gross_price AS ' . PriceIndexMap::PRICE . ',
                   \'€\' AS ' . PriceIndexMap::CURRENCY . ',
                   spy_deposit.bottles AS ' . ProductSearchConfig::KEY_PRODUCT_SEARCH_BOTTLES . ',
                   spy_deposit.volume_per_bottle AS ' . ProductSearchConfig::KEY_PRODUCT_SEARCH_VOLUME_PER_BOTTLE . '
            
            FROM spy_touch
            
                   LEFT JOIN spy_touch_search
                     ON ((spy_touch.id_touch = spy_touch_search.fk_touch AND spy_touch_search.fk_locale = :id_locale) AND spy_touch_search.fk_store = :id_store)
            
                   INNER JOIN merchant_price
                     ON (spy_touch.item_id = merchant_price.id_price)
            
                   INNER JOIN spy_product
                     ON (merchant_price.fk_product = spy_product.id_product)
            
                   INNER JOIN spy_deposit
                     ON (spy_deposit.id_deposit = spy_product.fk_deposit)
            
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