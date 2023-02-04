<?php
/**
 * Durst - project - TaxDependencyProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 18:43
 */

namespace Pyz\Zed\Tax;

use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Tax\TaxDependencyProvider as SprykerTaxDependencyProvider;

class TaxDependencyProvider extends SprykerTaxDependencyProvider
{
    public const QUERY_CONTAINER_TAX_PRODUCT = 'QUERY_CONTAINER_TAX_PRODUCT';

    public function provideCommunicationLayerDependencies(Container $container)
    {
        parent::provideCommunicationLayerDependencies($container);

        $container = $this->addTaxProductQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTaxProductQueryContainer(Container $container): Container
    {
        $container[static::QUERY_CONTAINER_TAX_PRODUCT] = function (Container $container) {
            return $container
                ->getLocator()
                ->taxProductConnector()
                ->queryContainer();
        };

        return $container;
    }

}
