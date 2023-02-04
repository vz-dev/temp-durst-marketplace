<?php
/**
 * Durst - project - AddressHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 14:56
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;


use Generated\Shared\Transfer\AddressTransfer;

interface AddressHydratorInterface
{
    /**
     * @param array $order
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function createAddressTransfer(array &$order): AddressTransfer;
}
