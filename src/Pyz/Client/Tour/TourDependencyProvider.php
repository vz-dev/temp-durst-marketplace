<?php


namespace Pyz\Client\Tour;


use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

class TourDependencyProvider extends AbstractDependencyProvider
{
    public const SERVICE_ZED = 'SERVICE_ZED';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container)
    {
        $container = $this->addZedRequestClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     * @return \Spryker\Client\Kernel\Container
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