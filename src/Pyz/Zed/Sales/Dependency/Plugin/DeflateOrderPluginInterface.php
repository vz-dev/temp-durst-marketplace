<?php

/**
 * Durst - project - DeflateOrderPluginInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-07-04
 * Time: 14:42
 */

namespace Pyz\Zed\Sales\Dependency\Plugin;

use Generated\Shared\Transfer\OrderTransfer;

/**
 * Interface DeflateOrderPluginInterface
 * @package Pyz\Zed\Sales\Dependency\Plugin
 */
interface DeflateOrderPluginInterface
{
    /**
     * Specification:
     *   - Its a plugin which deflate OrderTransfer when order read is persistence,
     *   - Can be used to deflate mutiple items to grouped items in OrderTransfer
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function deflate(OrderTransfer $orderTransfer);
}
