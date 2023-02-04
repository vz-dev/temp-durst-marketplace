<?php
/**
 * Durst - project - GMTimeSlotCollectorQuery.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.10.21
 * Time: 07:49
 */

namespace Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql;


use Pyz\Zed\Collector\CollectorConfig;
use Spryker\Zed\Collector\Persistence\Collector\AbstractPdoCollectorQuery;

class GMTimeSlotCollectorQuery extends AbstractPdoCollectorQuery
{
    public const ID_GM_TIME_SLOT = 'id_time_slot';
    public const START_TIME = 'start_time';
    public const END_TIME = 'end_time';

    /**
     * @var \Pyz\Zed\Collector\CollectorConfig
     */
    protected $config;

    /**
     * TimeSlotCollectorQuery constructor.
     * @param \Pyz\Zed\Collector\CollectorConfig $config
     */
    public function __construct(
        CollectorConfig $config
    )
    {
        $this->config = $config;
    }
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
                dst_graphmasters_time_slot.id_graphmasters_time_slot AS '.static::ID_GM_TIME_SLOT.',
                dst_graphmasters_time_slot.start_time AS '.static::START_TIME.',
                dst_graphmasters_time_slot.end_time AS '.static::END_TIME.'
            FROM
                spy_touch
                JOIN dst_graphmasters_time_slot
                    ON (spy_touch.item_id = dst_graphmasters_time_slot.id_graphmasters_time_slot)
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
