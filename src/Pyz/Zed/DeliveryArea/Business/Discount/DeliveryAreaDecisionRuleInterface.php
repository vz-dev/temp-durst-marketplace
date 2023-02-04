<?php
/**
 * Durst - project - DeliveryAreaDecisionRuleInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.09.20
 * Time: 09:13
 */

namespace Pyz\Zed\DeliveryArea\Business\Discount;

use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface DeliveryAreaDecisionRuleInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ClauseTransfer $clauseTransfer
     *
     * @return bool
     */
    public function isSatisfiedBy(QuoteTransfer $quoteTransfer, ClauseTransfer $clauseTransfer): bool;
}
