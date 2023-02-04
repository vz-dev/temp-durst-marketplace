<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 09.11.18
 * Time: 12:50
 */

namespace Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql;


use Generated\Shared\Search\BranchIndexMap;
use Spryker\Zed\Collector\Persistence\Collector\AbstractPdoCollectorQuery;

class BranchCollectorQuery extends AbstractPdoCollectorQuery
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
                   array_to_string(array_agg(DISTINCT spy_branch_to_payment_method.fk_payment_method), \',\') AS ' . BranchIndexMap::PAYMENT_PROVIDER_IDS . ',
                   spy_branch.id_branch AS ' . BranchIndexMap::ID_BRANCH . ',
                   spy_branch.name AS ' . BranchIndexMap::NAME . ',
                   spy_branch.street||\' \'||spy_branch.number AS ' . BranchIndexMap::STREET . ',
                   spy_branch.zip AS ' . BranchIndexMap::ZIP . ',
                   spy_branch.city AS ' . BranchIndexMap::CITY . ',
                   spy_branch.phone AS ' . BranchIndexMap::PHONE . ',
                   spy_branch.terms_of_service AS ' . BranchIndexMap::TERMS_OF_SERVICE . ',
                   spy_branch.company_profile AS ' . BranchIndexMap::COMPANY_PROFILE . '
            
            FROM spy_touch
            
                   LEFT JOIN spy_touch_search
                     ON ((spy_touch.id_touch = spy_touch_search.fk_touch AND spy_touch_search.fk_locale = :id_locale) AND spy_touch_search.fk_store = :id_store)
            
                   INNER JOIN spy_branch
                     ON (spy_touch.item_id = spy_branch.id_branch)
            
                   INNER JOIN spy_branch_to_payment_method
                     ON (spy_branch.id_branch = spy_branch_to_payment_method.fk_branch)
            
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
                spy_touch_search.id_touch_search,
                spy_branch.id_branch'
            )
            ->setParameter(':id_locale', $this->locale->getIdLocale())
            ->setParameter(':id_store', $this->storeTransfer->getIdStore());
    }
}