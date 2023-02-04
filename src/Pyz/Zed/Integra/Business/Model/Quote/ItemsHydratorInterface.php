<?php
/**
 * Durst - project - ItemsHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 19.11.20
 * Time: 15:01
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;


use Generated\Shared\Transfer\ItemTransfer;

interface ItemsHydratorInterface
{
    /**
     * @param string $positionDid
     * @param array $item
     * @param string $sku
     *
     * @return ItemTransfer
     */
    public function createItem(string $positionDid, array $item, string $sku): ItemTransfer;
}
