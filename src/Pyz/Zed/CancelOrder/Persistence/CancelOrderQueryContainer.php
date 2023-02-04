<?php
/**
 * Durst - project - CancelOrderQueryContainer.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 31.08.21
 * Time: 15:34
 */

namespace Pyz\Zed\CancelOrder\Persistence;

use Orm\Zed\CancelOrder\Persistence\DstCancelOrderQuery;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Class CancelOrderQueryContainer
 * @package Pyz\Zed\CancelOrder\Persistence
 *
 * @method CancelOrderPersistenceFactory getFactory()
 */
class CancelOrderQueryContainer extends AbstractQueryContainer implements CancelOrderQueryContainerInterface
{
    /**
     * @return \Orm\Zed\CancelOrder\Persistence\DstCancelOrderQuery
     */
    public function queryCancelOrder(): DstCancelOrderQuery
    {
        return $this
            ->getFactory()
            ->createCancelOrderQuery();
    }
}
