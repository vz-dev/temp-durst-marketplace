<?php
/**
 * Durst - project - GMSettingsCollectorQuery.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 18.10.21
 * Time: 17:50
 */

namespace Pyz\Zed\Collector\Persistence\Storage\Pdo\PostgreSql;


use Spryker\Zed\Collector\Persistence\Collector\AbstractPdoCollectorQuery;

class GMSettingsCollectorQuery extends AbstractPdoCollectorQuery
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
                dst_graphmasters_settings.id_graphmasters_settings,
                dst_graphmasters_settings.fk_branch,
                dst_graphmasters_settings.is_active,
                dst_graphmasters_settings.depot_api_id,
                dst_graphmasters_settings.depot_path,
                dst_graphmasters_settings.lead_time,
                dst_graphmasters_settings.buffer_time
            FROM
                spy_touch
                JOIN dst_graphmasters_settings
                    ON (spy_touch.item_id = dst_graphmasters_settings.fk_branch)
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
