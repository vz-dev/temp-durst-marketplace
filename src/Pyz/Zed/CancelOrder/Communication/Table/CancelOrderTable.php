<?php
/**
 * Durst - project - CancelOrderTable.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.09.21
 * Time: 15:32
 */

namespace Pyz\Zed\CancelOrder\Communication\Table;

use DateTime;
use DateTimeZone;
use Orm\Zed\CancelOrder\Persistence\Map\DstCancelOrderTableMap;
use Orm\Zed\Driver\Persistence\DstDriver;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Orm\Zed\Sales\Persistence\SpySalesOrderAddress;
use Orm\Zed\Tour\Persistence\Map\DstConcreteTourTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;
use Pyz\Zed\CancelOrder\CancelOrderConfig;
use Pyz\Zed\CancelOrder\Persistence\CancelOrderQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

/**
 * Class CancelOrderTable
 * @package Pyz\Zed\CancelOrder\Communication\Table
 */
class CancelOrderTable extends AbstractTable
{
    public const HEADER_CANCEL_ORDER_ID = 'Id';
    public const HEADER_CANCEL_ORDER_BRANCH = 'Branch';
    public const HEADER_CANCEL_ORDER_SALES_ORDER = 'Order';
    public const HEADER_CANCEL_ORDER_BILLING = 'Billing';
    public const HEADER_CANCEL_ORDER_SHIPPING = 'Shipping';
    public const HEADER_CANCEL_ORDER_CONCRETE_TOUR = 'Tour';
    public const HEADER_CANCEL_ORDER_DRIVER = 'Driver';
    public const HEADER_CANCEL_ORDER_MAIL = 'Mail';
    public const HEADER_CANCEL_ORDER_CREATED = 'Created';

    public const KEY_SALES_ORDER = 'order_reference';
    public const KEY_BRANCH_NAME = 'branch_name';
    public const KEY_BILLING_ADDRESS = 'billing_address';
    public const KEY_SHIPPING_ADDRESS = 'shipping_address';
    public const KEY_CONCRETE_TOUR = 'concrete_tour';
    public const KEY_DRIVER = 'driver';

    protected const DATE_FORMAT = 'd.m.Y H:i';
    protected const ADDRESS_FORMAT = '%s %s<br/>%s<br/>%s %s';

    /**
     * @var \Pyz\Zed\CancelOrder\Persistence\CancelOrderQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface
     */
    protected $facade;

    /**
     * @var \Pyz\Zed\CancelOrder\CancelOrderConfig
     */
    protected $cancelOrderConfig;

    /**
     * @param \Pyz\Zed\CancelOrder\Persistence\CancelOrderQueryContainerInterface $queryContainer
     * @param \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface $facade
     * @param \Pyz\Zed\CancelOrder\CancelOrderConfig $cancelOrderConfig
     */
    public function __construct(
        CancelOrderQueryContainerInterface $queryContainer,
        CancelOrderFacadeInterface $facade,
        CancelOrderConfig $cancelOrderConfig
    )
    {
        $this->queryContainer = $queryContainer;
        $this->facade = $facade;
        $this->cancelOrderConfig = $cancelOrderConfig;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config
            ->setHeader(
                [
                    DstCancelOrderTableMap::COL_ID_CANCEL_ORDER => static::HEADER_CANCEL_ORDER_ID,
                    static::KEY_BRANCH_NAME => static::HEADER_CANCEL_ORDER_BRANCH,
                    static::KEY_SALES_ORDER => static::HEADER_CANCEL_ORDER_SALES_ORDER,
                    static::KEY_BILLING_ADDRESS => static::HEADER_CANCEL_ORDER_BILLING,
                    static::KEY_SHIPPING_ADDRESS => static::HEADER_CANCEL_ORDER_SHIPPING,
                    static::KEY_CONCRETE_TOUR => static::HEADER_CANCEL_ORDER_CONCRETE_TOUR,
                    static::KEY_DRIVER => static::HEADER_CANCEL_ORDER_DRIVER,
                    DstCancelOrderTableMap::COL_EMAIL => static::HEADER_CANCEL_ORDER_MAIL,
                    DstCancelOrderTableMap::COL_CREATED_AT => static::HEADER_CANCEL_ORDER_CREATED
                ]
            );

        $config
            ->setSearchable(
                [
                    SpyBranchTableMap::COL_NAME,
                    DstCancelOrderTableMap::COL_EMAIL,
                    DstConcreteTourTableMap::COL_TOUR_REFERENCE,
                    SpySalesOrderTableMap::COL_ORDER_REFERENCE
                ]
            );

        $config
            ->setSortable(
                [
                    DstCancelOrderTableMap::COL_ID_CANCEL_ORDER,
                    static::KEY_BRANCH_NAME,
                    static::KEY_CONCRETE_TOUR,
                    DstCancelOrderTableMap::COL_EMAIL,
                    DstCancelOrderTableMap::COL_CREATED_AT
                ]
            );

        $config
            ->setRawColumns(
                [
                    static::KEY_BILLING_ADDRESS,
                    static::KEY_SHIPPING_ADDRESS,
                    static::KEY_DRIVER
                ]
            );

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $query = $this
            ->queryContainer
            ->queryCancelOrder()
            ->joinWithSpySalesOrder()
            ->joinWithDstConcreteTour()
            ->joinWithDstDriver(Criteria::LEFT_JOIN)
            ->innerJoinBillingAddress('billing')
            ->with('billing')
            ->innerJoinShippingAddress('shipping')
            ->with('shipping')
            ->useSpySalesOrderQuery()
                ->useSpyBranchQuery()
                    ->addAsColumn(
                        static::KEY_BRANCH_NAME,
                        SpyBranchTableMap::COL_NAME
                    )
                ->endUse()
            ->endUse()
            ->useDstConcreteTourQuery()
                ->addAsColumn(
                    static::KEY_CONCRETE_TOUR,
                    DstConcreteTourTableMap::COL_TOUR_REFERENCE
                )
            ->endUse();

        $queryResults = $this
            ->runQuery(
                $query,
                $config,
                true
            );

        $result = [];

        /* @var $queryResult \Orm\Zed\CancelOrder\Persistence\DstCancelOrder */
        foreach ($queryResults as $queryResult) {
            $result[] = [
                DstCancelOrderTableMap::COL_ID_CANCEL_ORDER => $queryResult->getIdCancelOrder(),
                static::KEY_BRANCH_NAME => $queryResult->getVirtualColumn(static::KEY_BRANCH_NAME),
                static::KEY_SALES_ORDER => $queryResult->getSpySalesOrder()->getOrderReference(),
                static::KEY_BILLING_ADDRESS => $this->getAddressInformation(
                    $queryResult
                        ->getBillingAddress()
                ),
                static::KEY_SHIPPING_ADDRESS => $this->getAddressInformation(
                    $queryResult
                        ->getShippingAddress()
                ),
                static::KEY_CONCRETE_TOUR => $queryResult->getVirtualColumn(static::KEY_CONCRETE_TOUR),
                static::KEY_DRIVER => $this->getDriverInformation(
                    $queryResult
                        ->getDstDriver()
                ),
                DstCancelOrderTableMap::COL_EMAIL => $queryResult->getEmail(),
                DstCancelOrderTableMap::COL_CREATED_AT => $this->formatDateTime(
                    $queryResult
                        ->getCreatedAt()
                )
            ];
        }

        return $result;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderAddress|null $address
     * @return string
     */
    protected function getAddressInformation(
        ?SpySalesOrderAddress $address
    ): string
    {
        if ($address === null) {
            return '';
        }

        return sprintf(
            static::ADDRESS_FORMAT,
            $address->getFirstName(),
            $address->getLastName(),
            $address->getAddress1(),
            $address->getZipCode(),
            $address->getCity()
        );
    }

    /**
     * @param \Orm\Zed\Driver\Persistence\DstDriver|null $driver
     * @return string
     */
    protected function getDriverInformation(
        ?DstDriver $driver
    ): string
    {
        if ($driver === null) {
            return '';
        }

        return sprintf(
            '%s %s<br />%s',
            $driver
                ->getFirstName(),
            $driver
                ->getLastName(),
            $driver
                ->getEmail()
        );
    }

    /**
     * @param \DateTime $dateTime
     * @return string
     */
    protected function formatDateTime(
        DateTime $dateTime
    ): string
    {
        $dateTime
            ->setTimezone(
                new DateTimeZone(
                    $this
                        ->cancelOrderConfig
                        ->getProjectTimezone()
                )
            );

        return $dateTime
            ->format(
                static::DATE_FORMAT
            );
    }
}
