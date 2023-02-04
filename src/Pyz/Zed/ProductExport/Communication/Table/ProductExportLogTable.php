<?php
/**
 * Durst - project - ProductExportLogTable.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.10.20
 * Time: 12:33
 */

namespace Pyz\Zed\ProductExport\Communication\Table;


use DateTime;
use DateTimeZone;
use Orm\Zed\ProductExport\Persistence\Base\DstProductExport;
use Orm\Zed\ProductExport\Persistence\Map\DstProductExportTableMap;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\ProductExport\Communication\Controller\LogController;
use Pyz\Zed\ProductExport\Persistence\ProductExportQueryContainerInterface;
use Pyz\Zed\ProductExport\ProductExportConfig;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class ProductExportLogTable extends AbstractTable
{
    protected const DATE_FORMAT = 'd.m.y H:i';

    protected const NOT_AVAILABLE = 'N/A';

    /**
     * @var ProductExportQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var ProductExportConfig
     */
    protected $projectExportConfig;

    /**
     * ProductExportLogTable constructor.
     * @param ProductExportQueryContainerInterface $queryContainer
     * @param ProductExportConfig $projectExportConfig
     */
    public function __construct(
        ProductExportQueryContainerInterface $queryContainer,
        ProductExportConfig $projectExportConfig
    )
    {
        $this->queryContainer = $queryContainer;
        $this->projectExportConfig = $projectExportConfig;
    }

    /**
     * @param TableConfiguration $config
     * @return TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config
            ->setHeader(
                [
                    DstProductExportTableMap::COL_ID_PRODUCT_EXPORT => 'Id',
                    DstProductExportTableMap::COL_FK_BRANCH => 'Händler',
                    DstProductExportTableMap::COL_RECIPIENT => 'Empfänger',
                    DstProductExportTableMap::COL_RECIPIENT_CC => 'Empfänger CC',
                    DstProductExportTableMap::COL_CNT_TOTAL_PRODUCTS => 'Produkte',
                    DstProductExportTableMap::COL_CNT_MERCHANT_PRODUCTS => 'Händlerprodukte',
                    DstProductExportTableMap::COL_STATUS => 'Status',
                    DstProductExportTableMap::COL_FILE_NAME => 'Datei',
                    DstProductExportTableMap::COL_CREATED_AT => 'Erstellt',
                    DstProductExportTableMap::COL_UPDATED_AT => 'Letzte Änderung'
                ]
            );

        $config
            ->setRawColumns(
                [
                    DstProductExportTableMap::COL_STATUS
                ]
            );

        $config
            ->setSortable(
                [
                    DstProductExportTableMap::COL_ID_PRODUCT_EXPORT,
                    DstProductExportTableMap::COL_FK_BRANCH,
                    DstProductExportTableMap::COL_RECIPIENT,
                    DstProductExportTableMap::COL_CREATED_AT
                ]
            );

        $config
            ->setDefaultSortField(
                DstProductExportTableMap::COL_CREATED_AT,
                TableConfiguration::SORT_DESC
            );

        $config
            ->setSearchable(
                [
                    DstProductExportTableMap::COL_RECIPIENT,
                    DstProductExportTableMap::COL_FK_BRANCH
                ]
            );

        return $config;
    }

    /**
     * @param TableConfiguration $config
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $query = $this
            ->queryContainer
            ->queryProductExport()
            ->joinWithSpyBranch();

        $queryResults = $this
            ->runQuery(
                $query,
                $config,
                true
            );

        $results = [];

        /* @var $queryResult \Orm\Zed\ProductExport\Persistence\DstProductExport */
        foreach ($queryResults as $queryResult) {
            $results[] = [
                DstProductExportTableMap::COL_ID_PRODUCT_EXPORT => $queryResult->getIdProductExport(),
                DstProductExportTableMap::COL_FK_BRANCH => $queryResult->getSpyBranch()->getName(),
                DstProductExportTableMap::COL_RECIPIENT => $queryResult->getRecipient(),
                DstProductExportTableMap::COL_RECIPIENT_CC => $queryResult->getRecipientCc(),
                DstProductExportTableMap::COL_CNT_TOTAL_PRODUCTS => $queryResult->getCntTotalProducts(),
                DstProductExportTableMap::COL_CNT_MERCHANT_PRODUCTS => $queryResult->getCntMerchantProducts(),
                DstProductExportTableMap::COL_STATUS => $this->createStatusButton($queryResult),
                DstProductExportTableMap::COL_FILE_NAME => $this->getFilename($queryResult->getFileName()),
                DstProductExportTableMap::COL_CREATED_AT => $this->getDateWithProjectTimezone($queryResult->getCreatedAt()),
                DstProductExportTableMap::COL_UPDATED_AT => $this->getDateWithProjectTimezone($queryResult->getUpdatedAt())
            ];
        }

        return $results;
    }

    /**
     * @param DateTime $dateTime
     * @return string
     */
    protected function getDateWithProjectTimezone(DateTime $dateTime): string
    {
        $projectTimezone = new DateTimeZone(
            $this
                ->projectExportConfig
                ->getProjectTimezone()
        );

        $dateTime
            ->setTimezone(
                $projectTimezone
            );

        return $dateTime
            ->format(
                static::DATE_FORMAT
            );
    }

    /**
     * @param string|null $path
     * @return string
     */
    protected function getFilename(?string $path): string
    {
        if ($path === null) {
            return static::NOT_AVAILABLE;
        }

        return pathinfo(
            $path,
            PATHINFO_BASENAME
        );
    }

    /**
     * @param DstProductExport $productExport
     * @return string
     * @throws PropelException
     */
    protected function createStatusButton(DstProductExport $productExport): string
    {
       switch ($productExport->getStatus()) {
           case DstProductExportTableMap::COL_STATUS_WAITING:
               return $this
                   ->createSuccessButton(
                       LogController::LOG_INDEX_URL,
                       'Waiting'
                   );
           case DstProductExportTableMap::COL_STATUS_RUNNING:
               return $this
                   ->createPendingButton(
                       LogController::LOG_INDEX_URL,
                       'Running'
                   );
           case DstProductExportTableMap::COL_STATUS_SENDING:
               return $this
                   ->createPendingButton(
                       LogController::LOG_INDEX_URL,
                       'Sending'
                   );
           case DstProductExportTableMap::COL_STATUS_DONE:
               return $this
                   ->createSuccessButton(
                   LogController::LOG_INDEX_URL,
                   'Done'
               );
           default:
           case DstProductExportTableMap::COL_STATUS_FAILED:
               return $this
                   ->createErrorButton(
                   LogController::LOG_INDEX_URL,
                   'Failed'
               );

       }
    }

    /**
     * @param string $url
     * @param string $title
     * @param array $options
     * @return string
     */
    protected function createSuccessButton(string $url, string $title, array $options = []): string
    {
        $defaultOptions = [
            'class' => 'btn-primary',
            'icon' => 'fa-check-square',
        ];

        return $this->generateButton(
            $url,
            $title,
            $defaultOptions,
            $options
        );
    }

    /**
     * @param string $url
     * @param string $title
     * @param array $options
     * @return string
     */
    protected function createPendingButton(string $url, string $title, array $options = []): string
    {
        $defaultOptions = [
            'class' => 'btn-warning',
            'icon' => 'fa-spinner',
        ];

        return $this->generateButton(
            $url,
            $title,
            $defaultOptions,
            $options
        );
    }

    /**
     * @param string $url
     * @param string $title
     * @param array $options
     * @return string
     */
    protected function createErrorButton(string $url, string $title, array $options = []): string
    {
        $defaultOptions = [
            'class' => 'btn-danger',
            'icon' => 'fa-exclamation-triangle',
        ];

        return $this->generateButton(
            $url,
            $title,
            $defaultOptions,
            $options
        );
    }
}
