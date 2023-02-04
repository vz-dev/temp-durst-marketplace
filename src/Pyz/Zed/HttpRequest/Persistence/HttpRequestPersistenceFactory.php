<?php
/**
 * Durst - project - HttpRequestPersistenceFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 12:50
 */

namespace Pyz\Zed\HttpRequest\Persistence;


use Orm\Zed\HttpRequest\Persistence\PyzHttpRequestQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class HttpRequestPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\HttpRequest\Persistence\PyzHttpRequestQuery
     */
    public function createHttpRequestQuery(): PyzHttpRequestQuery
    {
        return PyzHttpRequestQuery::create();
    }
}
