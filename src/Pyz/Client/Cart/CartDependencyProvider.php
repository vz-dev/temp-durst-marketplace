<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Client\Cart;

use Pyz\Client\Cart\Plugin\SessionQuoteStorageStrategyPlugin;
use Spryker\Client\Cart\CartDependencyProvider as SprykerCartDependencyProvider;
use Spryker\Client\Kernel\Container;
use Spryker\Client\ProductBundle\Plugin\Cart\ItemCountPlugin;

class CartDependencyProvider extends SprykerCartDependencyProvider
{
    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addItemCountPlugin(Container $container)
    {
        $container[static::PLUGIN_ITEM_COUNT] = function (Container $container) {
            return new ItemCountPlugin();
        };

        return $container;
    }

    /**
     * @return \Spryker\Client\CartExtension\Dependency\Plugin\QuoteStorageStrategyPluginInterface[]
     */
    protected function getQuoteStorageStrategyPlugins()
    {
        return [
            new SessionQuoteStorageStrategyPlugin(),
        ];
    }
}
