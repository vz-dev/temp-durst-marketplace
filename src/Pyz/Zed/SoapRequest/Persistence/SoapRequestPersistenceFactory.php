<?php
/**
 * Durst - project - SoapRequestPersistenceFactory.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-02
 * Time: 15:18
 */

namespace Pyz\Zed\SoapRequest\Persistence;


use Orm\Zed\SoapRequest\Persistence\DstSoapRequestQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class SoapRequestPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return DstSoapRequestQuery
     */
    public function createSoapRequestQuery(): DstSoapRequestQuery
    {
        return DstSoapRequestQuery::create();
    }
}
