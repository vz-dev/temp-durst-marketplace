<?php
/**
 * Durst - project - DeliveryAreaDecisionRulePlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.09.20
 * Time: 08:42
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Discount;

use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Discount\Business\QueryString\ComparatorOperators;
use Spryker\Zed\Discount\Dependency\Plugin\DecisionRulePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class DeliveryAreaDecisionRulePlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Discount
 * @method \Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface getFacade()
 * @method \Pyz\Zed\DeliveryArea\DeliveryAreaConfig getConfig()
 */
class DeliveryAreaDecisionRulePlugin extends AbstractPlugin implements DecisionRulePluginInterface
{
    public function isSatisfiedBy(
        QuoteTransfer $quoteTransfer,
        ItemTransfer $itemTransfer,
        ClauseTransfer $clauseTransfer
    ) {
        return $this
            ->getFacade()
            ->isSatisfiedByDeliveryArea($quoteTransfer, $itemTransfer, $clauseTransfer);
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this
            ->getConfig()
            ->getDeliveryAreaFieldName();
    }

    /**
     * @return array
     */
    public function acceptedDataTypes()
    {
        return [
            ComparatorOperators::TYPE_STRING,
            ComparatorOperators::TYPE_LIST,
        ];
    }
}
