<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Application;

use Pyz\Shared\Config\Environment;
use Pyz\Zed\CancelOrder\Communication\Plugin\ServiceProvider\TwigCancelOrderServiceProvider;
use Pyz\Zed\WebProfiler\Communication\Plugin\ServiceProvider\WebProfilerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Spryker\Service\UtilDateTime\ServiceProvider\DateTimeFormatterServiceProvider;
use Spryker\Shared\Application\ServiceProvider\FormFactoryServiceProvider;
use Spryker\Shared\ErrorHandler\Plugin\ServiceProvider\WhoopsErrorHandlerServiceProvider;
use Spryker\Zed\Acl\Communication\Plugin\Bootstrap\AclBootstrapProvider;
use Spryker\Zed\Api\Communication\Plugin\ApiServiceProviderPlugin;
use Spryker\Zed\Api\Communication\Plugin\ServiceProvider\ApiRoutingServiceProvider;
use Spryker\Zed\Application\ApplicationDependencyProvider as SprykerApplicationDependencyProvider;
use Spryker\Zed\Application\Communication\Plugin\ServiceProvider\EnvironmentInformationServiceProvider;
use Spryker\Zed\Application\Communication\Plugin\ServiceProvider\KernelLogServiceProvider;
use Spryker\Zed\Application\Communication\Plugin\ServiceProvider\MvcRoutingServiceProvider;
use Spryker\Zed\Application\Communication\Plugin\ServiceProvider\RequestServiceProvider;
use Spryker\Zed\Application\Communication\Plugin\ServiceProvider\RoutingServiceProvider;
use Spryker\Zed\Application\Communication\Plugin\ServiceProvider\SilexRoutingServiceProvider;
use Spryker\Zed\Application\Communication\Plugin\ServiceProvider\SslServiceProvider;
use Spryker\Zed\Application\Communication\Plugin\ServiceProvider\SubRequestServiceProvider;
use Spryker\Zed\Application\Communication\Plugin\ServiceProvider\TranslationServiceProvider;
use Spryker\Zed\Application\Communication\Plugin\ServiceProvider\ZedHstsServiceProvider;
use Spryker\Zed\Assertion\Communication\Plugin\ServiceProvider\AssertionServiceProvider;
use Spryker\Zed\Auth\Communication\Plugin\Bootstrap\AuthBootstrapProvider;
use Spryker\Zed\Auth\Communication\Plugin\ServiceProvider\RedirectAfterLoginProvider;
use Spryker\Zed\Currency\Communication\Plugin\ServiceProvider\TwigCurrencyServiceProvider;
use Spryker\Zed\Gui\Communication\Plugin\ServiceProvider\GuiTwigExtensionServiceProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Messenger\Communication\Plugin\ServiceProvider\MessengerServiceProvider;
use Spryker\Zed\Money\Communication\Plugin\ServiceProvider\TwigMoneyServiceProvider;
use Spryker\Zed\NewRelic\Communication\Plugin\ServiceProvider\NewRelicRequestTransactionServiceProvider;
use Spryker\Zed\Propel\Communication\Plugin\ServiceProvider\PropelServiceProvider;
use Spryker\Zed\Session\Communication\Plugin\ServiceProvider\SessionServiceProvider as SprykerSessionServiceProvider;
use Spryker\Zed\Twig\Communication\Plugin\ServiceProvider\TwigServiceProvider as SprykerTwigServiceProvider;
use Spryker\Zed\User\Communication\Plugin\ServiceProvider\UserServiceProvider;
use Spryker\Zed\ZedNavigation\Communication\Plugin\ServiceProvider\ZedNavigationServiceProvider;
use Spryker\Zed\ZedRequest\Communication\Plugin\GatewayServiceProviderPlugin;

class ApplicationDependencyProvider extends SprykerApplicationDependencyProvider
{
    const SERVICE_UTIL_DATE_TIME = 'util date time service';
    const SERVICE_NETWORK = 'util network service';
    const SERVICE_UTIL_IO = 'util io service';
    const SERVICE_DATA = 'util data service';

    const SERVICE_PROVIDER = 'SERVICE_PROVIDER';
    const SERVICE_PROVIDER_API = 'SERVICE_PROVIDER_API';
    const INTERNAL_CALL_SERVICE_PROVIDER = 'INTERNAL_CALL_SERVICE_PROVIDER';
    const INTERNAL_CALL_SERVICE_PROVIDER_WITH_AUTHENTICATION = 'INTERNAL_CALL_SERVICE_PROVIDER_WITH_AUTHENTICATION';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container[self::SERVICE_PROVIDER] = function (Container $container) {
            return $this->getServiceProviders($container);
        };

        $container[self::SERVICE_PROVIDER_API] = function (Container $container) {
            return $this->getApiServiceProviders($container);
        };

        $container[self::INTERNAL_CALL_SERVICE_PROVIDER] = function (Container $container) {
            return $this->getInternalCallServiceProviders($container);
        };

        $container[self::INTERNAL_CALL_SERVICE_PROVIDER_WITH_AUTHENTICATION] = function (Container $container) {
            return $this->getInternalCallServiceProvidersWithAuthentication($container);
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Silex\ServiceProviderInterface[]
     */
    protected function getServiceProviders(Container $container)
    {
        $coreProviders = parent::getServiceProviders($container);

        $providers = [
            new KernelLogServiceProvider(),
            new SessionServiceProvider(),
            new SprykerSessionServiceProvider(),
            new SslServiceProvider(),
            new AuthBootstrapProvider(),
            new AclBootstrapProvider(),
            new TwigServiceProvider(),
            new SprykerTwigServiceProvider(),
            new EnvironmentInformationServiceProvider(),
            new GatewayServiceProviderPlugin(),
            new AssertionServiceProvider(),
            new UserServiceProvider(),
            new TwigMoneyServiceProvider(),
            new SubRequestServiceProvider(),
            new WebProfilerServiceProvider(),
            new ZedHstsServiceProvider(),
            new FormFactoryServiceProvider(),
            new TwigCurrencyServiceProvider(),
            new MessengerServiceProvider(),
            new ZedNavigationServiceProvider(),
            new NewRelicRequestTransactionServiceProvider(),
            new TranslationServiceProvider(),
            new DateTimeFormatterServiceProvider(),
            new GuiTwigExtensionServiceProvider(),
            new RedirectAfterLoginProvider(),
            new PropelServiceProvider(),
            new TwigCancelOrderServiceProvider(),
        ];

        $providers = array_merge($providers, $coreProviders);

        return $providers;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Silex\ServiceProviderInterface[]
     */
    protected function getApiServiceProviders(Container $container)
    {
        $providers = [
            // Add Auth service providers
            new RequestServiceProvider(),
            new SslServiceProvider(),
            new ServiceControllerServiceProvider(),
            new RoutingServiceProvider(),
            new ApiServiceProviderPlugin,
            new ApiRoutingServiceProvider(),
            new PropelServiceProvider(),
        ];

        if (Environment::isDevelopment()) {
            $providers[] = new WhoopsErrorHandlerServiceProvider();
        }

        return $providers;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Silex\ServiceProviderInterface[]
     */
    protected function getInternalCallServiceProviders(Container $container)
    {
        return [
            new KernelLogServiceProvider(),
            new PropelServiceProvider(),
            new RequestServiceProvider(),
            new SslServiceProvider(),
            new ServiceControllerServiceProvider(),
            new RoutingServiceProvider(),
            new MvcRoutingServiceProvider(),
            new SilexRoutingServiceProvider(),
            new GatewayServiceProviderPlugin(),
            new NewRelicRequestTransactionServiceProvider(),
            new HttpFragmentServiceProvider(),
            new SubRequestServiceProvider(),
            new TwigServiceProvider(),
            new TwigMoneyServiceProvider(),
            new SprykerTwigServiceProvider(),
        ];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Silex\ServiceProviderInterface[]
     */
    protected function getInternalCallServiceProvidersWithAuthentication(Container $container)
    {
        return [
            new KernelLogServiceProvider(),
            new PropelServiceProvider(),
            new RequestServiceProvider(),
            new SessionServiceProvider(),
            new SprykerSessionServiceProvider(),
            new SslServiceProvider(),
            new AuthBootstrapProvider(),
            new AclBootstrapProvider(),
            new ServiceControllerServiceProvider(),
            new RoutingServiceProvider(),
            new MvcRoutingServiceProvider(),
            new SilexRoutingServiceProvider(),
            new GatewayServiceProviderPlugin(),
            new NewRelicRequestTransactionServiceProvider(),
            new HttpFragmentServiceProvider(),
            new SubRequestServiceProvider(),
            new TwigServiceProvider(),
            new TwigMoneyServiceProvider(),
            new SprykerTwigServiceProvider(),
        ];
    }
}
