<?php
/**
 * Durst - project - PaymentLogTable.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.02.19
 * Time: 13:30
 */

namespace Pyz\Zed\HeidelpayRest\Communication\Table;

use Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLog;
use Orm\Zed\HeidelpayRest\Persistence\Map\DstPaymentHeidelpayRestLogTableMap;
use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\HeidelpayRest\Communication\Controller\LogController;
use Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class PaymentLogTable extends AbstractTable
{
    protected const DATE_FORMAT = 'd.m.y H:i';

    protected const ORDER_DETAIL_URL = '/sales/detail';

    protected const PARAM_ID_SALES_ORDER = 'id-sales-order';

    /**
     * @var \Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * PaymentLogTable constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface $queryContainer
     */
    public function __construct(HeidelpayRestQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            SpySalesOrderTableMap::COL_ORDER_REFERENCE => 'Bestellnummer',
            DstPaymentHeidelpayRestLogTableMap::COL_TRANSACTION_TYPE => 'Transaktion',
            DstPaymentHeidelpayRestLogTableMap::COL_STATUS => 'Status',
            DstPaymentHeidelpayRestLogTableMap::COL_AMOUNT => 'Wert',
            DstPaymentHeidelpayRestLogTableMap::COL_ERROR_CODE => 'Error Code',
            DstPaymentHeidelpayRestLogTableMap::COL_ERROR_MESSAGE => 'Error',
            DstPaymentHeidelpayRestLogTableMap::COL_SHORT_ID => 'Short ID',
            DstPaymentHeidelpayRestLogTableMap::COL_CREATED_AT => 'Timestamp',
        ]);

        $config->setRawColumns([
            SpySalesOrderTableMap::COL_ORDER_REFERENCE,
            DstPaymentHeidelpayRestLogTableMap::COL_STATUS,
        ]);

        $config->setSortable([
            SpySalesOrderTableMap::COL_ORDER_REFERENCE,
            DstPaymentHeidelpayRestLogTableMap::COL_TRANSACTION_TYPE,
            DstPaymentHeidelpayRestLogTableMap::COL_STATUS,
            DstPaymentHeidelpayRestLogTableMap::COL_ERROR_CODE,
            DstPaymentHeidelpayRestLogTableMap::COL_CREATED_AT,
        ]);

        $config->setDefaultSortField(DstPaymentHeidelpayRestLogTableMap::COL_CREATED_AT, TableConfiguration::SORT_DESC);

        $config->setSearchable([
            SpySalesOrderTableMap::COL_ORDER_REFERENCE,
            DstPaymentHeidelpayRestLogTableMap::COL_ERROR_CODE,
            DstPaymentHeidelpayRestLogTableMap::COL_SHORT_ID,
            DstPaymentHeidelpayRestLogTableMap::COL_CREATED_AT,
            DstPaymentHeidelpayRestLogTableMap::COL_SHORT_ID,
        ]);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this
            ->queryContainer
            ->queryLog()
            ->joinWithSpySalesOrder();
        $queryResults = $this->runQuery($query, $config, true);

        $results = [];
        /** @var \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLog $logEntity */
        foreach ($queryResults as $logEntity) {
            $results[] = [
                SpySalesOrderTableMap::COL_ORDER_REFERENCE =>
                    $this->createOrderLink(
                        $logEntity->getSpySalesOrder()->getOrderReference(),
                        $logEntity->getSpySalesOrder()->getIdSalesOrder()
                    ),
                DstPaymentHeidelpayRestLogTableMap::COL_TRANSACTION_TYPE => $logEntity->getTransactionType(),
                DstPaymentHeidelpayRestLogTableMap::COL_STATUS => $this->createStatusButton($logEntity),
                DstPaymentHeidelpayRestLogTableMap::COL_ERROR_CODE => $this->getPropertyOrNa($logEntity->getErrorCode()),
                DstPaymentHeidelpayRestLogTableMap::COL_ERROR_MESSAGE => $this->getPropertyOrNa($logEntity->getErrorMessage()),
                DstPaymentHeidelpayRestLogTableMap::COL_SHORT_ID => $this->getPropertyOrNa($logEntity->getShortId()),
                DstPaymentHeidelpayRestLogTableMap::COL_AMOUNT => sprintf('%01.2f€', $logEntity->getAmount()),
                DstPaymentHeidelpayRestLogTableMap::COL_CREATED_AT => $logEntity->getCreatedAt()->format(self::DATE_FORMAT),
            ];
        }

        return $results;
    }

    /**
     * @param string $orderReference
     * @param int $idSalesOrder
     * @return string
     */
    protected function createOrderLink(
        string $orderReference,
        int $idSalesOrder
    ): string
    {
        $url = Url::generate(
            static::ORDER_DETAIL_URL,
            [
                static::PARAM_ID_SALES_ORDER => $idSalesOrder
            ]
        );

        $url = $url->buildEscaped();

        return sprintf(
            '<a href=%s>%s</a>',
            $url,
            $orderReference
        );
    }

    /**
     * @param \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLog $entity
     *
     * @return string
     */
    protected function createStatusButton(DstPaymentHeidelpayRestLog $entity): string
    {
        switch ($entity->getStatus()) {
            case HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_PENDING:
                return $this->createPendingButton(LogController::LOG_INDEX_URL, 'Pending');
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_TRANSACTION_STATUS_SUCCESS:
                return $this->createSuccessButton(LogController::LOG_INDEX_URL, 'Success');
                break;
            default:
                return $this->createErrorButton(LogController::LOG_INDEX_URL, 'Error');
        }
    }

    /**
     * @param string $url
     * @param string $title
     * @param array $options
     *
     * @return string
     */
    protected function createSuccessButton(string $url, string $title, array $options = []): string
    {
        $defaultOptions = [
            'class' => 'btn-primary',
            'icon' => 'fa-check-square',
        ];

        return $this->generateButton($url, $title, $defaultOptions, $options);
    }

    /**
     * @param string $url
     * @param string $title
     * @param array $options
     *
     * @return string
     */
    protected function createPendingButton(string $url, string $title, array $options = []): string
    {
        $defaultOptions = [
            'class' => 'btn-warning',
            'icon' => 'fa-spinner',
        ];

        return $this->generateButton($url, $title, $defaultOptions, $options);
    }

    /**
     * @param string $url
     * @param string $title
     * @param array $options
     *
     * @return string
     */
    protected function createErrorButton(string $url, string $title, array $options = []): string
    {
        $defaultOptions = [
            'class' => 'btn-danger',
            'icon' => 'fa-exclamation-triangle',
        ];

        return $this->generateButton($url, $title, $defaultOptions, $options);
    }

    /**
     * @param null|string $property
     *
     * @return string
     */
    protected function getPropertyOrNa(?string $property = null): string
    {
        if ($property === null) {
            return 'n/a';
        }

        return $property;
    }
}
