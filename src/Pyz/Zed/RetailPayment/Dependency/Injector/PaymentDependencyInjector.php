<?php
/**
 * Durst - project - PaymentDependencyInjector.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.05.18
 * Time: 12:21
 */

namespace Pyz\Zed\RetailPayment\Dependency\Injector;


use Pyz\Shared\RetailPayment\RetailPaymentConfig;
use Pyz\Zed\RetailPayment\Communication\Plugin\Checkout\RetailPaymentPostCheckPlugin;
use Pyz\Zed\RetailPayment\Communication\Plugin\Checkout\RetailPaymentPreCheckPlugin;
use Pyz\Zed\RetailPayment\Communication\Plugin\Checkout\RetailPaymentSaveOrderPlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Kernel\Dependency\Injector\AbstractDependencyInjector;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginCollection;
use Spryker\Zed\Payment\PaymentDependencyProvider;

class PaymentDependencyInjector extends AbstractDependencyInjector
{
    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function injectBusinessLayerDependencies(Container $container)
    {
        $container = $this->injectPaymentPlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function injectPaymentPlugins(Container $container)
    {
        $container->extend(PaymentDependencyProvider::CHECKOUT_PLUGINS, function (CheckoutPluginCollection $pluginCollection) {
            $pluginCollection->add(new RetailPaymentPreCheckPlugin(), RetailPaymentConfig::PROVIDER_NAME, PaymentDependencyProvider::CHECKOUT_PRE_CHECK_PLUGINS);
            $pluginCollection->add(new RetailPaymentSaveOrderPlugin(), RetailPaymentConfig::PROVIDER_NAME, PaymentDependencyProvider::CHECKOUT_ORDER_SAVER_PLUGINS);
            $pluginCollection->add(new RetailPaymentPostCheckPlugin(), RetailPaymentConfig::PROVIDER_NAME, PaymentDependencyProvider::CHECKOUT_POST_SAVE_PLUGINS);

            return $pluginCollection;
        });

        return $container;
    }
}