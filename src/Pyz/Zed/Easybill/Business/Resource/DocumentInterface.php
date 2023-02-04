<?php
/**
 * Durst - project - DocumentInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.01.20
 * Time: 16:43
 */

namespace Pyz\Zed\Easybill\Business\Resource;

interface DocumentInterface
{
    /**
     * @param int $customerId
     *
     * @return \Generated\Shared\Transfer\string
     */
    public function createInvoice(int $customerId): string;
}
