<?php
/**
 * Durst - project - QuoteStorageStrategyPluginInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.09.18
 * Time: 16:10
 */

namespace Pyz\Client\CartExtension\Dependency\Plugin;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\CartExtension\Dependency\Plugin\QuoteStorageStrategyPluginInterface as SprykerQuoteStorageStrategyPluginInterface;
use stdClass;

interface QuoteStorageStrategyPluginInterface extends SprykerQuoteStorageStrategyPluginInterface
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
     * @param ItemTransfer[]|array $itemTransfers
     * @param BranchTransfer $branchTransfer
     * @param ConcreteTimeSlotTransfer[]|array $concreteTimeSlotTransfers
     * @param stdClass|null $requestObject
     * @return QuoteTransfer
     */
    public function addItemsForBranchAndConcreteTimeSlots(
        array $itemTransfers,
        BranchTransfer $branchTransfer,
        array $concreteTimeSlotTransfers,
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
