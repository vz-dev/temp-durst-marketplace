<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * Durst - Marketplace-Platform - RetailPaymentDependencyProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 02.03.18
 * Time: 11:41
 */

namespace Pyz\Zed\RetailPayment;


use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * Class RetailPaymentDependencyProvider
 * @package Pyz\Zed\RetailPayment
 */
class RetailPaymentDependencyProvider extends AbstractBundleDependencyProvider
{
    const FACADE_MERCHANT = 'FACADE_MERCHANT';

    /**
     * @param Container $container
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addMerchantFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantFacade(Container $container) : Container
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }
}