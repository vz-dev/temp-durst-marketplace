<?php
/**
 * Durst - project - SoapRequestTable.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-02
 * Time: 15:37
 */

namespace Pyz\Zed\SoapRequest\Communication\Table;


use Orm\Zed\SoapRequest\Persistence\Map\DstSoapRequestTableMap;
use Pyz\Zed\SoapRequest\Persistence\SoapRequestQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class SoapRequestTable extends AbstractTable
{
    protected const ACTION = 'Action';

    protected const VIEW_SOAP_REQUEST_URL = '/soap-request/index/view';

    public const PARAM_ID_SOAP_REQUEST = 'id';

    protected const COL_HAS_ERROR = 'has_error';

    /**
     * @var SoapRequestQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * HttpRequestTable constructor.
     * @param SoapRequestQueryContainerInterface $queryContainer
     */
    public function __construct(
        SoapRequestQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param TableConfiguration $config
     *
     * @return TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config
            ->setHeader([
                DstSoapRequestTableMap::COL_ID_SOAP_REQUEST => 'ID',
                DstSoapRequestTableMap::COL_REQUEST_SERVICE => 'Uri',
                DstSoapRequestTableMap::COL_REQUEST_FUNCTION => 'Function',
                DstSoapRequestTableMap::COL_RESPONSE_CODE => 'Code',
                self::COL_HAS_ERROR => 'Error',
                DstSoapRequestTableMap::COL_CREATED_AT =>'Created at',
                self::ACTION => self::ACTION
            ]);

        $config
            ->setRawColumns([
                self::ACTION,
                self::COL_HAS_ERROR,
            ]);

        $config
            ->setDefaultSortField(
                DstSoapRequestTableMap::COL_ID_SOAP_REQUEST, TableConfiguration::SORT_DESC
            );

        $config
            ->setSortable([
                DstSoapRequestTableMap::COL_ID_SOAP_REQUEST,
                DstSoapRequestTableMap::COL_REQUEST_FUNCTION,
                DstSoapRequestTableMap::COL_REQUEST_SERVICE,
                DstSoapRequestTableMap::COL_RESPONSE_CODE,
                self::COL_HAS_ERROR,
                DstSoapRequestTableMap::COL_CREATED_AT
            ]);

        $config
            ->setSearchable([
                DstSoapRequestTableMap::COL_ID_SOAP_REQUEST,
                DstSoapRequestTableMap::COL_REQUEST_SERVICE,
                DstSoapRequestTableMap::COL_REQUEST_FUNCTION,
                DstSoapRequestTableMap::COL_RESPONSE_CODE,
            ]);

        return $config;
    }

    /**
     * @param TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $query = $this
            ->queryContainer
            ->querySoapRequest();

        $queryResults = $this
            ->runQuery(
                $query,
                $config
            );

        $results = [];
        foreach ($queryResults as $queryResult) {
            $results[] = [
                DstSoapRequestTableMap::COL_ID_SOAP_REQUEST => $queryResult[DstSoapRequestTableMap::COL_ID_SOAP_REQUEST],
                DstSoapRequestTableMap::COL_REQUEST_SERVICE => $queryResult[DstSoapRequestTableMap::COL_REQUEST_SERVICE],
                DstSoapRequestTableMap::COL_REQUEST_FUNCTION => $queryResult[DstSoapRequestTableMap::COL_REQUEST_FUNCTION],
                DstSoapRequestTableMap::COL_RESPONSE_CODE => $queryResult[DstSoapRequestTableMap::COL_RESPONSE_CODE],
                self::COL_HAS_ERROR => $this->soapRequestHasError($queryResult[DstSoapRequestTableMap::COL_RESPONSE_ERROR]),
                DstSoapRequestTableMap::COL_CREATED_AT => $queryResult[DstSoapRequestTableMap::COL_CREATED_AT],
                self::ACTION => implode(' ', $this->createActionButtons($queryResult))
            ];
        }

        return $results;
    }

    /**
     * @param array $httpRequestArray
     * @return array
     */
    protected function createActionButtons(array $httpRequestArray): array
    {
        $urls = [];

        $urls[] = $this
            ->generateViewButton(
                Url::generate(
                    self::VIEW_SOAP_REQUEST_URL,
                    [
                        self::PARAM_ID_SOAP_REQUEST => $httpRequestArray[DstSoapRequestTableMap::COL_ID_SOAP_REQUEST]
                    ]
                ),
                'View'
            );

        return $urls;
    }

    /**
     * @param string|null $requestError
     * @return string
     */
    protected function soapRequestHasError(?string  $requestError) : string
    {
        if(strlen($requestError) > 0)
        {
            return '<i class="fa fa-bug" aria-hidden="true"></i>';
        }

        return '-';
    }
}
