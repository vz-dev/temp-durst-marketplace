<?php
/**
 * Durst - project - DeliveryAreaDecisionRule.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.09.20
 * Time: 08:59
 */

namespace Pyz\Zed\DeliveryArea\Business\Discount;

use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;

class DeliveryAreaDecisionRule implements DeliveryAreaDecisionRuleInterface
{
    /**
     * @var \Pyz\Zed\Discount\Business\DiscountFacadeInterface
     */
    protected $discountFacade;

    /**
     * DeliveryAreaDecisionRule constructor.
     *
     * @param \Pyz\Zed\Discount\Business\DiscountFacadeInterface $discountFacade
     */
    public function __construct(DiscountFacadeInterface $discountFacade)
    {
        $this->discountFacade = $discountFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ClauseTransfer $clauseTransfer
     *
     * @return bool
     */
    public function isSatisfiedBy(QuoteTransfer $quoteTransfer, ClauseTransfer $clauseTransfer): bool
    {
        return $this
                ->discountFacade
                ->queryStringCompare(
                    $clauseTransfer,
                    $quoteTransfer->getShippingAddress()->getZipCode()
                );
    }
}
