<?php
/**
 * Durst - project - MinValueExpander.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.05.18
 * Time: 16:38
 */

namespace Pyz\Zed\DeliveryArea\Business\Manager;

use Generated\Shared\Transfer\CartChangeTransfer;

class MinValueExpander
{
    /**
     * @param CartChangeTransfer $cartChangeTransfer
     *
     * @return CartChangeTransfer
     */
    public function expandItemsByMinValue(CartChangeTransfer $cartChangeTransfer): CartChangeTransfer
    {
        $cartChangeTransfer
            ->requireQuote();
        $quote = $cartChangeTransfer->getQuote();

        if($quote->getUseFlexibleTimeSlots() === true)
        {
            $quote->setMinValue(0);
            return $cartChangeTransfer;
        }

        $cartChangeTransfer
            ->getQuote()
            ->requireConcreteTimeSlots();

        $quote->setMinValue($quote->getConcreteTimeSlots()->offsetGet(0)->getMinValue());

        return $cartChangeTransfer;
    }
}
