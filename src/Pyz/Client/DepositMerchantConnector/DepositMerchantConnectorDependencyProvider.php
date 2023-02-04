<?php
/**
 * Durst - project - DepositMerchantConnecorDependencyProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 12:42
 */

namespace Pyz\Client\DepositMerchantConnector;


use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

class DepositMerchantConnectorDependencyProvider extends AbstractDependencyProvider
{
    public const CLIENT_ZED_REQUEST = 'CLIENT_ZED_REQUEST';

    /**
     * {@inheritDoc}
     *
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
        $container[self::CLIENT_ZED_REQUEST] = function (Container $container) {
            return $container
                ->getLocator()
                ->zedRequest()
                ->client();
        };

        return $container;
    }
}