<?php
/**
 * Durst - project - CartItemDepositPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.04.18
 * Time: 10:14
 */

namespace Pyz\Zed\Deposit\Communication\Plugin;


use Generated\Shared\Transfer\CartChangeTransfer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Spryker\Zed\Cart\Dependency\ItemExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class CartItemDepositPlugin
 * @package Pyz\Zed\Deposit\Communication\Plugin
 * @method DepositFacadeInterface getFacade()
 */
class CartItemDepositPlugin extends AbstractPlugin implements ItemExpanderPluginInterface
{
    /**
     * @param CartChangeTransfer $cartChangeTransfer
     * @return CartChangeTransfer
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer)
    {
        return $this
            ->getFacade()
            ->addDepositToItem($cartChangeTransfer);
    }
}