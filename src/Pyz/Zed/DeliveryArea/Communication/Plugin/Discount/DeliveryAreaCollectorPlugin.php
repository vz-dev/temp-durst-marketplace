<?php
/**
 * Durst - project - DeliveryAreaCollectorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.09.20
 * Time: 09:22
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Discount;


use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Discount\Business\QueryString\ComparatorOperators;
use Spryker\Zed\Discount\Dependency\Plugin\CollectorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class DeliveryAreaCollectorPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Discount
 * @method \Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface getFacade()
 * @method \Pyz\Zed\DeliveryArea\DeliveryAreaConfig getConfig()
 */
class DeliveryAreaCollectorPlugin extends AbstractPlugin implements CollectorPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ClauseTransfer $clauseTransfer
     * @return \Generated\Shared\Transfer\DiscountableItemTransfer[]
     */
    public function collect(
        QuoteTransfer $quoteTransfer,
        ClauseTransfer $clauseTransfer
    )
    {
        return $this
            ->getFacade()
            ->collectByDeliveryArea($quoteTransfer, $clauseTransfer);
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
