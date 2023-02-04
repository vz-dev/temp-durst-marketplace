<?php
/**
 * Durst - project - HttpRequestQueryContainer.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 12:49
 */

namespace Pyz\Zed\HttpRequest\Persistence;


use Orm\Zed\HttpRequest\Persistence\PyzHttpRequestQuery;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Class HttpRequestQueryContainer
 * @package Pyz\Zed\HttpRequest\Persistence
 * @method HttpRequestPersistenceFactory getFactory()
 */
class HttpRequestQueryContainer extends AbstractQueryContainer implements HttpRequestQueryContainerInterface
{

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\HttpRequest\Persistence\PyzHttpRequestQuery
     */
    public function queryHttpRequest(): PyzHttpRequestQuery
    {
        return $this
            ->getFactory()
            ->createHttpRequestQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idHttpRequest
     * @return \Orm\Zed\HttpRequest\Persistence\PyzHttpRequestQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryHttpRequestById(int $idHttpRequest): PyzHttpRequestQuery
    {
        return $this
            ->getFactory()
            ->createHttpRequestQuery()
            ->filterByIdHttpRequest($idHttpRequest);
    }
}
