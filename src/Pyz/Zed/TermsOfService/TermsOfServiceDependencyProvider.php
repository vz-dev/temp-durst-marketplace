<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 11.01.18
 * Time: 15:41
 */

namespace Pyz\Zed\TermsOfService;


use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class TermsOfServiceDependencyProvider extends AbstractBundleDependencyProvider
{
    const FACADE_MERCHANT = 'FACADE_MERCHANT';

    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addMerchantFacade($container);

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function addMerchantFacade(Container $container){
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }
}