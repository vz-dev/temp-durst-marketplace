<?php
/**
 * Durst - project - HttpRequestTable.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.19
 * Time: 10:42
 */

namespace Pyz\Zed\HttpRequest\Communication\Table;


use Orm\Zed\HttpRequest\Persistence\Map\PyzHttpRequestTableMap;
use Pyz\Zed\HttpRequest\Persistence\HttpRequestQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class HttpRequestTable extends AbstractTable
{
    protected const ACTION = 'Action';

    protected const VIEW_HTTP_REQUEST_URL = '/http-request/index/view';

    public const PARAM_ID_HTTP_REQUEST = 'id';

    /**
     * @var \Pyz\Zed\HttpRequest\Persistence\HttpRequestQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * HttpRequestTable constructor.
     * @param \Pyz\Zed\HttpRequest\Persistence\HttpRequestQueryContainerInterface $queryContainer
     */
    public function __construct(
        HttpRequestQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config
            ->setHeader([
                PyzHttpRequestTableMap::COL_ID_HTTP_REQUEST => 'ID',
                PyzHttpRequestTableMap::COL_REQUEST_URI => 'Uri',
                PyzHttpRequestTableMap::COL_REQUEST_METHOD => 'Method',
                PyzHttpRequestTableMap::COL_RESPONSE_CODE => 'Code',
                PyzHttpRequestTableMap::COL_RESPONSE_MESSAGE => 'Message',
                PyzHttpRequestTableMap::COL_CREATED_AT =>'Created at',
                self::ACTION => self::ACTION
            ]);

        $config
            ->setRawColumns([
                self::ACTION
            ]);

        $config
            ->setDefaultSortField(
                PyzHttpRequestTableMap::COL_ID_HTTP_REQUEST, TableConfiguration::SORT_DESC
            );

        $config
            ->setSortable([
                PyzHttpRequestTableMap::COL_ID_HTTP_REQUEST,
                PyzHttpRequestTableMap::COL_REQUEST_METHOD,
                PyzHttpRequestTableMap::COL_RESPONSE_CODE
            ]);

        $config
            ->setSearchable([
                PyzHttpRequestTableMap::COL_REQUEST_URI,
                PyzHttpRequestTableMap::COL_RESPONSE_MESSAGE
            ]);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $query = $this
            ->queryContainer
            ->queryHttpRequest();
        $queryResults = $this
            ->runQuery(
                $query,
                $config
            );

        $results = [];
        foreach ($queryResults as $queryResult) {
            $results[] = [
                PyzHttpRequestTableMap::COL_ID_HTTP_REQUEST => $queryResult[PyzHttpRequestTableMap::COL_ID_HTTP_REQUEST],
                PyzHttpRequestTableMap::COL_REQUEST_URI => $queryResult[PyzHttpRequestTableMap::COL_REQUEST_URI],
                PyzHttpRequestTableMap::COL_REQUEST_METHOD => $queryResult[PyzHttpRequestTableMap::COL_REQUEST_METHOD],
                PyzHttpRequestTableMap::COL_RESPONSE_CODE => $queryResult[PyzHttpRequestTableMap::COL_RESPONSE_CODE],
                PyzHttpRequestTableMap::COL_RESPONSE_MESSAGE => $queryResult[PyzHttpRequestTableMap::COL_RESPONSE_MESSAGE],
                PyzHttpRequestTableMap::COL_CREATED_AT => $queryResult[PyzHttpRequestTableMap::COL_CREATED_AT],
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
                    self::VIEW_HTTP_REQUEST_URL,
                    [
                        self::PARAM_ID_HTTP_REQUEST => $httpRequestArray[PyzHttpRequestTableMap::COL_ID_HTTP_REQUEST]
                    ]
                ),
                'View'
            );

        return $urls;
    }
}
