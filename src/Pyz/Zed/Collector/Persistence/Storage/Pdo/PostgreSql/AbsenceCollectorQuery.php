<?php
/**
 * Durst - project - AbsenceCollectorQuery.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 28.12.21
 * Time: 15:38
 */

namespace Pyz\Zed\Collector\Persistence\Storage\Pdo\PostgreSql;


use Spryker\Zed\Collector\Persistence\Collector\AbstractPdoCollectorQuery;

class AbsenceCollectorQuery extends AbstractPdoCollectorQuery
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
                spy_absence.id_absence,
                spy_absence.fk_branch,
                spy_absence.start_date,
                spy_absence.end_date,
                spy_absence.description
            FROM
                spy_touch
                JOIN spy_absence
                    ON (spy_touch.item_id = spy_absence.fk_branch)
            WHERE
                spy_touch.item_type = :spy_touch_item_type
            AND
            spy_touch.item_event = :spy_touch_item_event
            AND
            spy_touch.touched >= :spy_touch_touched
        ';

        $this->criteriaBuilder
            ->sql($sql);
    }
}
