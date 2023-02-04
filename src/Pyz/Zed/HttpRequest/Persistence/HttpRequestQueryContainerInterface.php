<?php
/**
 * Durst - project - HttpRequestQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 12:49
 */

namespace Pyz\Zed\HttpRequest\Persistence;


use Orm\Zed\HttpRequest\Persistence\PyzHttpRequestQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface HttpRequestQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @return \Orm\Zed\HttpRequest\Persistence\PyzHttpRequestQuery
     */
    public function queryHttpRequest(): PyzHttpRequestQuery;

    /**
     * @param int $idHttpRequest
     * @return \Orm\Zed\HttpRequest\Persistence\PyzHttpRequestQuery
     */
    public function queryHttpRequestById(int $idHttpRequest): PyzHttpRequestQuery;
}
