<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 15.10.18
 * Time: 14:28
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Cart;


use Generated\Shared\Transfer\CartChangeTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Cart\Dependency\ItemExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class CartMinUnitsPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Cart
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class CartMinUnitsPlugin extends AbstractPlugin implements ItemExpanderPluginInterface
{

    /**
     * @param CartChangeTransfer $cartChangeTransfer
     * @return CartChangeTransfer
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer)
    {
        return $this
            ->getFacade()
            ->expandItemsByMinUnits($cartChangeTransfer);
    }
}