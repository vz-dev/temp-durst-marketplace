<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 13.11.18
 * Time: 10:13
 */

namespace Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql;


use Generated\Shared\Search\PaymentProviderIndexMap;
use Spryker\Zed\Collector\Persistence\Collector\AbstractPdoCollectorQuery;

class PaymentProviderCollectorQuery extends AbstractPdoCollectorQuery
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
                   spy_payment_method.id_payment_method AS ' . PaymentProviderIndexMap::ID_PAYMENT_METHOD . ',
                   spy_payment_method.name AS ' . PaymentProviderIndexMap::NAME . ',
                   spy_payment_method.code AS ' . PaymentProviderIndexMap::CODE . '
            
            FROM spy_touch
            
                   LEFT JOIN spy_touch_search
                     ON ((spy_touch.id_touch = spy_touch_search.fk_touch AND spy_touch_search.fk_locale = :id_locale) AND spy_touch_search.fk_store = :id_store)
            
                   INNER JOIN spy_payment_method
                     ON (spy_touch.item_id = spy_payment_method.id_payment_method)
            
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