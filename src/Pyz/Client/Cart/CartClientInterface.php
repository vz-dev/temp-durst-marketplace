<?php
/**
 * Durst - project - CartClientInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 27.04.18
 * Time: 12:51
 */

namespace Pyz\Client\Cart;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Cart\CartClientInterface as SprykerCartClientInterface;
use stdClass;

interface CartClientInterface extends SprykerCartClientInterface
{
    /**
     * @param ItemTransfer[]|array $itemTransfers
     * @param BranchTransfer $branchTransfer
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @param stdClass|null $requestObject
     * @return QuoteTransfer
     */
    public function addItemsForBranchAndConcreteTimeSlot(
        array $itemTransfers,
        BranchTransfer $branchTransfer,
        ConcreteTimeSlotTransfer $concreteTimeSlotTransfer,
        stdClass $requestObject = null
    ): QuoteTransfer;

    /**
     * @param array $itemTransfers
     * @param BranchTransfer $branchTransfer
     * @param stdClass|null $requestObject
     * @return QuoteTransfer
     */
    public function addItemsForBranchFlexTimeSlots(
        array $itemTransfers,
        BranchTransfer $branchTransfer,
        stdClass $requestObject = null
    ): QuoteTransfer;
}
