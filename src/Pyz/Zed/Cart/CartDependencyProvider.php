<?php
/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Cart;

use Pyz\Zed\DeliveryArea\Communication\Plugin\Cart\CartDeliveryCostPlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Cart\CartMinUnitsPlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Cart\CartMinValuePlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Cart\ConcreteTimeSlotAssertionPreConditionPlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Cart\TimeSlotsItemExpanderPlugin;
use Pyz\Zed\Deposit\Communication\Plugin\CartItemDepositPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\BranchIsActivePreCheckPlugin;
use Pyz\Zed\MerchantPrice\Communication\Plugin\CartItemPricePlugin;
use Spryker\Zed\Cart\CartDependencyProvider as SprykerCartDependencyProvider;
use Spryker\Zed\Cart\Communication\Plugin\SkuGroupKeyPlugin;
use Spryker\Zed\Cart\Dependency\CartPreCheckPluginInterface;
use Spryker\Zed\Cart\Dependency\ItemExpanderPluginInterface;
use Spryker\Zed\Cart\Dependency\PostSavePluginInterface;
use Spryker\Zed\Cart\Dependency\PreReloadItemsPluginInterface;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\PaymentCartConnector\Communication\Plugin\Cart\RemovePaymentCartPostSavePlugin;
use Spryker\Zed\ProductCartConnector\Communication\Plugin\ProductCartPlugin;
use Spryker\Zed\ProductCartConnector\Communication\Plugin\ProductExistsCartPreCheckPlugin;
use Spryker\Zed\ProductOptionCartConnector\Communication\Plugin\ChangeProductOptionQuantityPlugin;
use Spryker\Zed\ShipmentCartConnector\Communication\Plugin\Cart\CartShipmentExpanderPlugin;

class CartDependencyProvider extends SprykerCartDependencyProvider
{
    /**
     * @param Container $container
     *
     * @return ItemExpanderPluginInterface[]
     */
    protected function getExpanderPlugins(Container $container)
    {
        return [
            new ProductCartPlugin(),
            new CartItemPricePlugin(),
            new CartItemDepositPlugin(),
            new CartDeliveryCostPlugin(),
            new CartShipmentExpanderPlugin(),
            new CartMinValuePlugin(),
            new CartMinUnitsPlugin(),
            new SkuGroupKeyPlugin(),
        ];
    }

    /**
     * @param Container $container
     *
     * @return CartPreCheckPluginInterface[]
     */
    protected function getCartPreCheckPlugins(Container $container)
    {
        return [
            new ProductExistsCartPreCheckPlugin(),
            new BranchIsActivePreCheckPlugin(),
            new ConcreteTimeSlotAssertionPreConditionPlugin(),
        ];
    }

    /**
     * @param Container $container
     *
     * @return PostSavePluginInterface[]
     */
    protected function getPostSavePlugins(Container $container)
    {
        return [
            new ChangeProductOptionQuantityPlugin(),
            new RemovePaymentCartPostSavePlugin(),
        ];
    }

    /**
     * @param Container $container
     *
     * @return PreReloadItemsPluginInterface[]
     */
    protected function getPreReloadPlugins(Container $container)
    {
        return [
        ];
    }
}
