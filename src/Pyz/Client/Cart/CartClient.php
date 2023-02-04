<?php
/**
 * Durst - project - CartClient.php.
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
use Spryker\Client\Cart\CartClient as SprykerCartClient;
use stdClass;

/**
 * Class CartClient
 * @package Pyz\Client\Cart
 * @method CartFactory getFactory()
 */
class CartClient extends SprykerCartClient implements CartClientInterface
{
    /**
     * {@inheritdoc}
     *
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
    ): QuoteTransfer {
        return $this
            ->getFactory()
            ->getQuoteStorageStrategy()
            ->addItemsForBranchAndConcreteTimeSlot(
                $itemTransfers,
                $branchTransfer,
                $concreteTimeSlotTransfer,
                $requestObject
            );
    }

    /**
     * {@inheritdoc}
     *
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
    ): QuoteTransfer {
        return $this
            ->getFactory()
            ->getQuoteStorageStrategy()
            ->addItemsForBranchAndConcreteTimeSlots(
                $itemTransfers,
                $branchTransfer,
                $concreteTimeSlotTransfers,
                $requestObject
            );
    }

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
    ): QuoteTransfer {
        return $this
            ->getFactory()
            ->getQuoteStorageStrategy()
            ->addItemsForBranchFlexTimeSlots(
                $itemTransfers,
                $branchTransfer,
                $requestObject
            );
    }
}
