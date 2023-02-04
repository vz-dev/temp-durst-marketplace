<?php
/**
 * Durst - project - MerchantDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.12.21
 * Time: 11:29
 */

namespace Pyz\Client\Merchant;

use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

class MerchantDependencyProvider extends AbstractDependencyProvider
{
    public const SERVICE_ZED = 'zed service';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container): Container
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
