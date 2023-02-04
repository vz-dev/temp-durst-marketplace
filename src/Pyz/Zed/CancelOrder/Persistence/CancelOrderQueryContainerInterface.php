<?php
/**
 * Durst - project - CancelOrderQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 15:33
 */

namespace Pyz\Zed\CancelOrder\Persistence;

use Orm\Zed\CancelOrder\Persistence\DstCancelOrderQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

/**
 * Interface CancelOrderQueryContainerInterface
 * @package Pyz\Zed\CancelOrder\Persistence
 */
interface CancelOrderQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @return \Orm\Zed\CancelOrder\Persistence\DstCancelOrderQuery
     */
    public function queryCancelOrder(): DstCancelOrderQuery;
}
