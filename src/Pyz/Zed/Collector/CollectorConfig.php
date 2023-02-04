<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Collector;

use Pyz\Shared\Collector\CollectorConstants;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\Collector\Persistence\Search\Pdo\MySql\CategoryNodeCollectorQuery as MySqlSearchCategoryNodeCollectorQuery;
use Pyz\Zed\Collector\Persistence\Search\Pdo\MySql\ProductCollectorQuery as MySqlSearchProductCollector;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\BranchCollectorQuery;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\CategoryCollectorQuery;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\CategoryNodeCollectorQuery as PostgreSqlSearchCategoryNodeCollectorQuery;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\DeliveryAreaCollectorQuery;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\GMTimeSlotCollectorQuery;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\PaymentProviderCollectorQuery;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\PriceCollectorQuery;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\ProductCollectorQuery;
use Pyz\Zed\Collector\Persistence\Search\Pdo\PostgreSql\TimeSlotCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\MySql\CategoryNodeCollectorQuery as MySqlStorageCategoryNodeCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\MySql\NavigationCollectorQuery as MySqlNavigationCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\MySql\ProductAbstractCollectorQuery as MySqlProductAbstractCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\MySql\ProductConcreteCollectorQuery as MySqlProductConcreteCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\MySql\ProductOptionCollectorQuery as MySqlProductOptionCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\MySql\UrlCollectorQuery as MySqlUrlCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\PostgreSql\AbsenceCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\PostgreSql\CategoryNodeCollectorQuery as PostgreSqlStorageCategoryNodeCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\PostgreSql\GMSettingsCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\PostgreSql\NavigationCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\PostgreSql\ProductAbstractCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\PostgreSql\ProductConcreteCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\PostgreSql\ProductOptionCollectorQuery;
use Pyz\Zed\Collector\Persistence\Storage\Pdo\PostgreSql\UrlCollectorQuery;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Collector\CollectorConfig as SprykerCollectorConfig;

class CollectorConfig extends SprykerCollectorConfig
{
    public const DEFAULT_MAX_CUSTOMERS_AND_PRODUCTS_VALIDATION_STATE_BLACKLIST = [
        'order.state.declined',
        'order.state.confirmed',
        'order.state.closed',
    ];

    /**
     * @param string $dbEngineName
     *
     * @return array
     */
    public function getStoragePdoQueryAdapterClassNames($dbEngineName)
    {
        $data = [
            'MySql' => [
                'ProductCollectorQuery' => MySqlProductAbstractCollectorQuery::class,
                'ProductConcreteCollectorQuery' => MySqlProductConcreteCollectorQuery::class,
                'CategoryNodeCollectorQuery' => MySqlStorageCategoryNodeCollectorQuery::class,
                'NavigationCollectorQuery' => MySqlNavigationCollectorQuery::class,
                'UrlCollectorQuery' => MySqlUrlCollectorQuery::class,
                'ProductOptionCollectorQuery' => MySqlProductOptionCollectorQuery::class,
            ],
            'PostgreSql' => [
                'ProductCollectorQuery' => ProductAbstractCollectorQuery::class,
                'ProductConcreteCollectorQuery' => ProductConcreteCollectorQuery::class,
                'CategoryNodeCollectorQuery' => PostgreSqlStorageCategoryNodeCollectorQuery::class,
                'NavigationCollectorQuery' => NavigationCollectorQuery::class,
                'UrlCollectorQuery' => UrlCollectorQuery::class,
                'ProductOptionCollectorQuery' => ProductOptionCollectorQuery::class,
                'GMSettingsCollectorQuery' => GMSettingsCollectorQuery::class,
                'AbsenceCollectorQuery' => AbsenceCollectorQuery::class,
            ],
        ];

        return $data[$dbEngineName];
    }

    /**
     * @return string
     */
    public function getProjectTimeZone(): string
    {
        return $this
            ->get(ApplicationConstants::PROJECT_TIMEZONE);
    }

    /**
     * @param string $dbEngineName
     *
     * @return array
     */
    public function getSearchPdoQueryAdapterClassNames($dbEngineName)
    {
        $data = [
            'MySql' => [
                'ProductCollectorQuery' => MySqlSearchProductCollector::class,
                'CategoryNodeCollectorQuery' => MySqlSearchCategoryNodeCollectorQuery::class,
            ],
            'PostgreSql' => [
                //'ProductCollectorQuery' => PostgreSqlSearchProductCollector::class,
                'CategoryNodeCollectorQuery' => PostgreSqlSearchCategoryNodeCollectorQuery::class,
                'DeliveryAreaCollectorQuery' => DeliveryAreaCollectorQuery::class,
                'BranchCollectorQuery' => BranchCollectorQuery::class,
                'PriceCollectorQuery' => PriceCollectorQuery::class,
                'CategoryCollectorQuery' => CategoryCollectorQuery::class,
                'ProductCollectorQuery' => ProductCollectorQuery::class,
                'PaymentProviderCollectorQuery' => PaymentProviderCollectorQuery::class,
                'TimeSlotCollectorQuery' => TimeSlotCollectorQuery::class,
                'GMTimeSlotCollectorQuery' => GMTimeSlotCollectorQuery::class,
            ],
        ];

        return $data[$dbEngineName];
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     *
     * @return bool
     */
    public function getEnablePrepareScopeKeyJoinFixFeatureFlag()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTimeSlotSearchIndexName(): string
    {
        return $this
            ->get(CollectorConstants::ELASTICA_PARAMETER__TIME_SLOT_INDEX_NAME);
    }

    /**
     * @return string
     */
    public function getTimeSlotSearchDocumentType(): string
    {
        return $this
            ->get(CollectorConstants::ELASTICA_PARAMETER__TIME_SLOT_DOCUMENT_TYPE);
    }

    /**
     * @return array
     */
    public function getMaxProductValidationStateBlackList(): array
    {
        return $this
            ->get(DeliveryAreaConstants::MAX_CUSTOMERS_AND_PRODUCTS_VALIDATION_STATE_BLACKLIST, self::DEFAULT_MAX_CUSTOMERS_AND_PRODUCTS_VALIDATION_STATE_BLACKLIST);
    }

    /**
     * @return string
     */
    public function getGMTimeSlotSearchIndexName(): string
    {
        return $this
            ->get(CollectorConstants::ELASTICA_PARAMETER__GM_TIME_SLOT_INDEX_NAME);
    }

    /**
     * @return string
     */
    public function getGMTimeSlotSearchDocumentType(): string
    {
        return $this
            ->get(CollectorConstants::ELASTICA_PARAMETER__GM_TIME_SLOT_DOCUMENT_TYPE);
    }
}
