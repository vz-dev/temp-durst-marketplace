<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 14.05.19
 * Time: 08:18
 */

namespace Pyz\Client\ProductGtin;


use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

class ProductGtinDependencyProvider extends AbstractDependencyProvider
{
    public const SERVICE_ZED = 'SERVICE_ZED';

    /**
     * @param Container $container
     * @return Container
     */
    public function provideServiceLayerDependencies(Container $container): Container
    {
        $container = $this->addZedRequestClient($container);

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addZedRequestClient(Container $container): Container
    {
        $container[static::SERVICE_ZED] = function (Container $container) {
            return $container
                ->getLocator()
                ->zedRequest()
                ->client();
        };

        return $container;
    }
}
