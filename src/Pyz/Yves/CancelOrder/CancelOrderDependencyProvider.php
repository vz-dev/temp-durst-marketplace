<?php
/**
 * Durst - project - CancelOrderDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 14.09.21
 * Time: 15:27
 */

namespace Pyz\Yves\CancelOrder;

use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;

class CancelOrderDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_SALES = 'CLIENT_SALES';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addSalesClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addSalesClient(Container $container): Container
    {
        $container[static::CLIENT_SALES] = function (Container $container) {
            return $container
                ->getLocator()
                ->sales()
                ->client();
        };

        return $container;
    }
}
