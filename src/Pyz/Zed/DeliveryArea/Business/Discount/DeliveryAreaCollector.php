<?php
/**
 * Durst - project - DeliveryAreaCollector.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.09.20
 * Time: 09:13
 */

namespace Pyz\Zed\DeliveryArea\Business\Discount;

use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\DiscountableItemTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class DeliveryAreaCollector implements DeliveryAreaCollectorInterface
{
    /**
     * @var \Pyz\Zed\DeliveryArea\Business\Discount\DeliveryAreaDecisionRuleInterface
     */
    protected $deliveryAreaDecisionRule;

    /**
     * DeliveryAreaCollector constructor.
     *
     * @param \Pyz\Zed\DeliveryArea\Business\Discount\DeliveryAreaDecisionRuleInterface $deliveryAreaDecisionRule
     */
    public function __construct(
        DeliveryAreaDecisionRuleInterface $deliveryAreaDecisionRule
    ) {
        $this->deliveryAreaDecisionRule = $deliveryAreaDecisionRule;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ClauseTransfer $clauseTransfer
     *
     * @return \Generated\Shared\Transfer\DiscountableItemTransfer[]
     */
    public function collect(QuoteTransfer $quoteTransfer, ClauseTransfer $clauseTransfer): array
    {
        $discountableItems = [];
        if($this->deliveryAreaDecisionRule->isSatisfiedBy($quoteTransfer, $clauseTransfer)){
            foreach ($quoteTransfer->getItems() as $itemTransfer) {
                $discountableItems[] = $this
                    ->createDiscountableItemTransfer($itemTransfer, $quoteTransfer->getPriceMode());
            }
        }

        return $discountableItems;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param string $priceMode
     *
     * @return \Generated\Shared\Transfer\DiscountableItemTransfer
     */
    protected function createDiscountableItemTransfer(ItemTransfer $itemTransfer, string $priceMode)
    {
        $discountableItemTransfer = new DiscountableItemTransfer();
        $discountableItemTransfer->fromArray($itemTransfer->toArray(), true);
        $price = $this->getPrice($itemTransfer, $priceMode);
        $discountableItemTransfer->setUnitPrice($price);
        $discountableItemTransfer->setUnitGrossPrice($price);
        $discountableItemTransfer->setOriginalItemCalculatedDiscounts($itemTransfer->getCalculatedDiscounts());
        $discountableItemTransfer->setOriginalItem($itemTransfer);

        return $discountableItemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param string $priceMode
     *
     * @return int
     */
    protected function getPrice(ItemTransfer $itemTransfer, string $priceMode)
    {
        if ($priceMode === 'NET_MODE') {
            return $itemTransfer->getUnitNetPrice();
        } else {
            return $itemTransfer->getUnitGrossPrice();
        }
    }
}
