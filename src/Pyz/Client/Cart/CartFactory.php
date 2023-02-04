<?php
/**
 * Durst - project - CartFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.09.18
 * Time: 16:28
 */

namespace Pyz\Client\Cart;

use Pyz\Client\CartExtension\Dependency\Plugin\QuoteStorageStrategyPluginInterface;
use Spryker\Client\Cart\CartFactory as SprykerCartFactory;

class CartFactory extends SprykerCartFactory
{
    /**
     * @return \Spryker\Client\CartExtension\Dependency\Plugin\QuoteStorageStrategyPluginInterface|QuoteStorageStrategyPluginInterface
     */
    public function getQuoteStorageStrategy()
    {
        return $this->createQuoteStorageStrategyProvider()->provideStorage();
    }
}