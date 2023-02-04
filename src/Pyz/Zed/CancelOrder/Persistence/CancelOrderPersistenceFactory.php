<?php
/**
 * Durst - project - CancelOrderPersistenceFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 15:31
 */

namespace Pyz\Zed\CancelOrder\Persistence;

use Orm\Zed\CancelOrder\Persistence\DstCancelOrderQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * Class CancelOrderPersistenceFactory
 * @package Pyz\Zed\CancelOrder\Persistence
 */
class CancelOrderPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\CancelOrder\Persistence\DstCancelOrderQuery
     */
    public function createCancelOrderQuery(): DstCancelOrderQuery
    {
        return DstCancelOrderQuery::create();
    }
}
