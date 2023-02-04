<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Collector\Persistence\Storage\Pdo\PostgreSql;

use Spryker\Zed\Collector\Persistence\Collector\AbstractPdoCollectorQuery;

class ProductAbstractCollectorQuery extends AbstractPdoCollectorQuery
{
    const COL_IS_IN_STORE = 'is_in_store';

    /**
     * @return void
     */
    protected function prepareQuery()
    {
        $sql = '
SELECT
  spy_product_abstract.id_product_abstract AS id_product_abstract,
  spy_product_abstract.sku AS sku,
  spy_product_abstract_localized_attributes.name AS name,
  spy_product_abstract.attributes AS abstract_attributes,
  spy_product_abstract.color_code AS color_code,
  spy_url.url AS url,
  spy_product_abstract_localized_attributes.attributes AS abstract_localized_attributes,
  spy_product_abstract_localized_attributes.description AS description,
  spy_product_abstract_localized_attributes.meta_title AS meta_title,
  spy_product_abstract_localized_attributes.meta_keywords AS meta_keywords,
  spy_product_abstract_localized_attributes.meta_description AS meta_description,
  spy_product_abstract_store.fk_store AS ' . static::COL_IS_IN_STORE . ',
  t.id_touch AS %s,
  t.item_id AS %s,
  spy_touch_storage.id_touch_storage AS %s
FROM spy_touch t
  INNER JOIN spy_product_abstract ON (t.item_id = spy_product_abstract.id_product_abstract)
  LEFT JOIN spy_product_abstract_store ON (spy_product_abstract.id_product_abstract = spy_product_abstract_store.fk_product_abstract AND spy_product_abstract_store.fk_store = :id_store)
  INNER JOIN spy_product_abstract_localized_attributes ON (spy_product_abstract.id_product_abstract = spy_product_abstract_localized_attributes.fk_product_abstract)
  INNER JOIN spy_locale ON (spy_locale.id_locale = :fk_locale_1 and spy_locale.id_locale = spy_product_abstract_localized_attributes.fk_locale)
  LEFT JOIN spy_url ON (spy_product_abstract.id_product_abstract = spy_url.fk_resource_product_abstract AND spy_url.fk_locale = spy_locale.id_locale)
  LEFT JOIN spy_touch_storage ON (spy_touch_storage.fk_touch = t.id_touch AND spy_touch_storage.fk_locale = spy_locale.id_locale AND spy_touch_storage.fk_store = :id_store)
WHERE
  t.item_event = :spy_touch_item_event
  AND t.touched >= :spy_touch_touched
  AND t.item_type = :spy_touch_item_type
';
        $this->criteriaBuilder
            ->sql($sql)
            ->setParameter('fk_locale_1', $this->locale->getIdLocale())
            ->setParameter('id_store', $this->storeTransfer->getIdStore());
    }
}
