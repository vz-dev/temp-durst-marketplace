<?php


namespace Pyz\Zed\Sales\Communication\Plugin\Sales;


use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * Class ItemGtinHydratorPlugin
 * @package Pyz\Zed\Sales\Communication\Plugin\Sales
 * @method SalesFacadeInterface getFacade()
 */
class ItemGtinHydratorPlugin extends AbstractPlugin implements HydrateOrderPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrate(OrderTransfer $orderTransfer)
    {
        return $this
            ->getFacade()
            ->hydrateItemGtin($orderTransfer);
    }

}