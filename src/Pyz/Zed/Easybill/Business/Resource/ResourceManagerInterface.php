<?php
/**
 * Durst - project - ResourceManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.01.20
 * Time: 16:36
 */

namespace Pyz\Zed\Easybill\Business\Resource;

interface ResourceManagerInterface
{
    /**
     * @return bool
     */
    public function createInvoice(): bool;
}
