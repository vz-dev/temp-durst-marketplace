<?php
/**
 * Created by PhpStorm.
 * User: Giuliano
 * Date: 29.01.18
 * Time: 15:37
 */

namespace Pyz\Zed\Absence;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class AbsenceDependencyProvider extends AbstractBundleDependencyProvider
{
    const FACADE_MERCHANT = 'FACADE_MERCHANT';
    const FACADE_TOUCH = 'FACADE_TOUCH';

    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addMerchantFacade($container);

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addMerchantFacade(Container $container)
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }
}
