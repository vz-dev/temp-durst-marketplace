<?php
/**
 * Durst - project - HeidelpayRestPersistenceFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.01.19
 * Time: 10:27
 */

namespace Pyz\Zed\HeidelpayRest\Persistence;

use Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery;
use Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class HeidelpayRestPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestQuery
     */
    public function createHeidelpayRestPaymentQuery(): DstPaymentHeidelpayRestQuery
    {
        return DstPaymentHeidelpayRestQuery::create();
    }

    /**
     * @return \Orm\Zed\HeidelpayRest\Persistence\DstPaymentHeidelpayRestLogQuery
     */
    public function createHeidelpayRestLogQuery(): DstPaymentHeidelpayRestLogQuery
    {
        return DstPaymentHeidelpayRestLogQuery::create();
    }
}