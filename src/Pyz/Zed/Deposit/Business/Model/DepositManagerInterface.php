<?php
/**
 * Durst - project - DepositManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.09.18
 * Time: 09:25
 */

namespace Pyz\Zed\Deposit\Business\Model;

use Generated\Shared\Transfer\CartChangeTransfer;

interface DepositManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @throws \Pyz\Zed\Deposit\Business\Exception\DepositMissingException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function addDepositToItems(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer;
}
