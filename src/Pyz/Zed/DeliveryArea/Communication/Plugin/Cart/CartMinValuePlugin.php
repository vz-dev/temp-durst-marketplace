<?php
/**
 * Durst - project - CartMinValuePlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.05.18
 * Time: 16:42
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Cart;


use Generated\Shared\Transfer\CartChangeTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Cart\Dependency\ItemExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class CartMinValuePlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Cart
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class CartMinValuePlugin extends AbstractPlugin implements ItemExpanderPluginInterface
{
    /**
     * @param CartChangeTransfer $cartChangeTransfer
     * @return CartChangeTransfer
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer)
    {
        return $this
            ->getFacade()
            ->expandItemsByMinValue($cartChangeTransfer);
    }
}