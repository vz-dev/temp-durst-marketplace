<?php
/**
 * Durst - project - AppRestApiDependencyProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.04.18
 * Time: 14:02
 */

namespace Pyz\Zed\AppRestApi;


use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class AppRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    const FACADE_DELIVERY_AREA = 'FACADE_DELIVERY_AREA';
    const FACADE_MERCHANT_PRICE = 'FACADE_MERCHANT_PRICE';

    /**
     * @param Container $container
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addDeliveryAreaFacade($container);
        $container = $this->addMerchantPriceFacade($container);

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addDeliveryAreaFacade(Container $container) : Container
    {
        $container[static::FACADE_DELIVERY_AREA] = function (Container $container) {
            return $container->getLocator()->deliveryArea()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addMerchantPriceFacade(Container $container) : Container
    {
        $container[static::FACADE_MERCHANT_PRICE] = function (Container $container) {
            return $container->getLocator()->merchantPrice()->facade();
        };

        return $container;
    }
}