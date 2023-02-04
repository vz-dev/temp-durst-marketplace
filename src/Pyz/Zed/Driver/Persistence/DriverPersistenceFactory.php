<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 23.08.18
 * Time: 10:14
 */

namespace Pyz\Zed\Driver\Persistence;


use Orm\Zed\Driver\Persistence\DstDriverQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class DriverPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return DstDriverQuery
     */
    public function createDriverQuery() : DstDriverQuery
    {
        return DstDriverQuery::create();
    }
}
