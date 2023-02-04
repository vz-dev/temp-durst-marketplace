<?php
/**
 * Durst - project - ConcreteTimeSlotAssertionPreConditionPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.06.18
 * Time: 11:48
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Cart;

use Generated\Shared\Transfer\CartChangeTransfer;
use Spryker\Zed\CartExtension\Dependency\Plugin\CartPreCheckPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class ConcreteTimeSlotAssertionPreConditionPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout
 * @method \Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface getFacade()
 */
class ConcreteTimeSlotAssertionPreConditionPlugin extends AbstractPlugin implements CartPreCheckPluginInterface
{
    /**
     * Specification:
     * - This plugin is executed before cart add operation is executed,
     *   for example could be used to check if item quantity is available for selected item
     *   Should return CartPreCheckResponseTransfer where error messages set and flag that check failed.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     *
     * @return \Generated\Shared\Transfer\CartPreCheckResponseTransfer
     */
    public function check(CartChangeTransfer $cartChangeTransfer)
    {
        return $this
            ->getFacade()
            ->validateConcreteTimeSlotAssertionsForCheckout($cartChangeTransfer);
    }
}
