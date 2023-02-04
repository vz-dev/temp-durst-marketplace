<?php
/**
 * Durst - project - TotalsHydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.11.20
 * Time: 10:00
 */

namespace Pyz\Zed\Integra\Business\Model\Quote;


use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;

interface TotalsHydratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\TotalsTransfer
     */
    public function createTotalsTransfer(QuoteTransfer $quoteTransfer): TotalsTransfer;
}
