<?php
/**
 * Durst - project - CartItemPricePlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 27.04.18
 * Time: 11:03
 */

namespace Pyz\Zed\MerchantPrice\Communication\Plugin;


use Generated\Shared\Transfer\CartChangeTransfer;
use Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface;
use Spryker\Zed\Cart\Dependency\ItemExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class CartItemPricePlugin
 * @package Pyz\Zed\MerchantPrice\Communication\Plugin
 * @method MerchantPriceFacadeInterface getFacade()
 */
class CartItemPricePlugin extends AbstractPlugin implements ItemExpanderPluginInterface
{
    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    public function expandItems(CartChangeTransfer $cartChangeTransfer)
    {
        return $this->getFacade()->addPriceToItem($cartChangeTransfer);
    }
}