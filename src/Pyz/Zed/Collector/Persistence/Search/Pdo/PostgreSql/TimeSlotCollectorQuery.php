<?php
/**
 * Durst - project - TimeSlotCollectorQuery.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-07-01
 * Time: 22:20
 */

namespace Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql;

use Pyz\Zed\Collector\CollectorConfig;
use Spryker\Zed\Collector\Persistence\Collector\AbstractPdoCollectorQuery;

class TimeSlotCollectorQuery extends AbstractPdoCollectorQuery
{
    public const FK_TIME_SLOT = 'fktimeslot';
    public const ID_BRANCH = 'idbranch';
    public const MIN_VALUE_FIRST = 'minvalufirst';
    public const MIN_VALUE_FOLLOWING = 'minvaluefollowing';
    public const PREP_TIME = 'preptime';
    public const DELIVERY_COST = 'deliverycost';
    public const MAX_CUSTOMERS = 'maxcustomers';
    public const MAX_PRODUCTS = 'maxproducts';
    public const MIN_UNITS = 'minunits';
    public const ID_TIME_SLOT = 'idtimeslot';
    public const ID_TOUR = 'idtour';
    public const START = 'starttime';
    public const END = 'endtime';
    public const ZIP_CODE = 'zipcode';
    public const ALLOWED_WEIGHT = 'allowedweight';
    public const VIRTUAL_CUSTOMERS = 'customers';
    public const VIRTUAL_PRODUCTS = 'products';
    public const VIRTUAL_WEIGHT = 'weight';

    public const PAYLOAD_KG_FOR_RETAIL = 999999;

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
            WITH sales_order_items_cte AS (
                SELECT *
                FROM
                  spy_sales_order
                INNER JOIN spy_sales_order_item ON (spy_sales_order.id_sales_order = spy_sales_order_item.fk_sales_order
                    AND spy_sales_order_item.fk_oms_order_item_state NOT IN (
                        SELECT
                            spy_oms_order_item_state.id_oms_order_item_state
                        FROM
                            spy_oms_order_item_state
                        WHERE
                            spy_oms_order_item_state.name IN ('.$this->getValidationStateBlackList().')
                        )
                    )
                LEFT JOIN spy_concrete_time_slot ON spy_sales_order.fk_concrete_timeslot = spy_concrete_time_slot.id_concrete_time_slot
                WHERE spy_concrete_time_slot.start_time > NOW()
            )

            SELECT
                spy_touch.id_touch,
                spy_touch.item_type,
                spy_touch.item_event,
                spy_touch.item_id,
                spy_touch.touched,
                spy_touch.id_touch AS collector_touch_id,
                spy_touch.item_id AS collector_resource_id,
                spy_concrete_time_slot.fk_time_slot AS '.static::FK_TIME_SLOT.',
                spy_concrete_time_slot.id_concrete_time_slot AS '.static::ID_TIME_SLOT.',
                spy_time_slot.fk_branch AS '.static::ID_BRANCH.',
                spy_time_slot.min_value_first AS '.static::MIN_VALUE_FIRST.',
                spy_time_slot.min_value_following AS '.static::MIN_VALUE_FOLLOWING.',
                spy_time_slot.prep_time AS '.static::PREP_TIME.',
                spy_time_slot.delivery_costs AS '.static::DELIVERY_COST.',
                spy_time_slot.max_customers AS '.static::MAX_CUSTOMERS.',
                COALESCE(spy_time_slot.max_products, 999999) AS '.static::MAX_PRODUCTS.',
                spy_time_slot.min_units AS '.static::MIN_UNITS.',
                spy_concrete_time_slot.start_time AS '.static::START.',
                spy_concrete_time_slot.end_time AS '.static::END.',
                spy_delivery_area.zip_code AS '.static::ZIP_CODE.',
                dst_concrete_tour.id_concrete_tour AS '.static::ID_TOUR.',
                COALESCE(dst_vehicle_type.payload_kg, 999999) AS '.static::ALLOWED_WEIGHT.',
                (
                    SELECT
                        COUNT(DISTINCT sales_order_items_cte.id_sales_order)
                    FROM
                        sales_order_items_cte
                        WHERE
                            spy_concrete_time_slot.id_concrete_time_slot = sales_order_items_cte.fk_concrete_timeslot
                ) AS customers,
                (
                    SELECT
                        COALESCE(SUM(sales_order_items_cte.quantity), 0)
                    FROM
                        sales_order_items_cte
                        WHERE
                            spy_concrete_time_slot.id_concrete_time_slot = sales_order_items_cte.fk_concrete_timeslot
                ) AS products,
                (
                SELECT
                        COALESCE(SUM(sales_order_items_cte.quantity * spy_deposit.weight), 0)
                    FROM
                        sales_order_items_cte
                            LEFT JOIN spy_product ON (spy_product.sku = sales_order_items_cte.sku)
                            LEFT JOIN spy_deposit ON (spy_deposit.id_deposit = spy_product.fk_deposit)
                        WHERE
                            dst_concrete_tour.id_concrete_tour = sales_order_items_cte.fk_concrete_tour
                ) AS weight
            FROM
                spy_touch
                INNER JOIN spy_concrete_time_slot
                    ON (spy_touch.item_id = spy_concrete_time_slot.id_concrete_time_slot)
                INNER JOIN spy_time_slot
                    ON (spy_concrete_time_slot.fk_time_slot = spy_time_slot.id_time_slot)
                INNER JOIN spy_delivery_area
                    ON (spy_time_slot.fk_delivery_area = spy_delivery_area.id_delivery_area)
                LEFT JOIN dst_concrete_tour
                    ON (spy_concrete_time_slot.fk_concrete_tour = dst_concrete_tour.id_concrete_tour)
                LEFT JOIN dst_abstract_tour
                    ON (dst_concrete_tour.fk_abstract_tour = dst_abstract_tour.id_abstract_tour)
                LEFT JOIN dst_vehicle_type
                    ON (dst_abstract_tour.fk_vehicle_type = dst_vehicle_type.id_vehicle_type)

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

    /**
     * @return string
     */
    protected function getValidationStateBlackList() : string
    {
        return implode(
            ',',
            array_map(function($value) {
                return "'" . $value . "'";
            },
                $this
                    ->config
                    ->getMaxProductValidationStateBlackList()
            )
        );
    }
}
