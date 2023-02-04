<?php
/**
 * Durst - project - DiscountToCalculationBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.09.20
 * Time: 08:46
 */

namespace Pyz\Zed\Discount\Dependency\Facade;


use Generated\Shared\Transfer\QuoteTransfer;

interface DiscountToCalculationBridgeInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function recalculateQuote(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
