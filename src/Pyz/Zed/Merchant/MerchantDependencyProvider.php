<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 11.10.18
 * Time: 13:36
 */

namespace Pyz\Zed\Merchant;

use Pyz\Zed\Accounting\Communication\Plugin\Merchant\AccountingLicenseMerchantSaverPlugin;
use Pyz\Zed\Accounting\Communication\Plugin\Merchant\RealaxDebitorMerchantSaverPlugin;
use Pyz\Zed\Billing\Communication\Plugin\Merchant\BillingBranchPreSaverPlugin;
use Pyz\Zed\Billing\Communication\Plugin\Merchant\BillingMerchantSaverPlugin;
use Pyz\Zed\Billing\Communication\Plugin\Merchant\CsvExportBranchPreSaverPlugin;
use Pyz\Zed\DepositPickup\Communication\Plugin\Merchant\OffersDepositPickupBranchPreSaverPlugin;
use Pyz\Zed\Edifact\Communication\Plugin\Merchant\EdiExcludeMissingItemReturnsBranchPreSaverPlugin;
use Pyz\Zed\Edifact\Communication\Plugin\Merchant\EdiExportVersionBranchPreSaverPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\BranchUser\BranchHydratorPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\BranchUser\MerchantHydratorPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\MerchantUser\BranchHydratorPlugin as MUBranchHydratorPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\MerchantUser\MerchantHydratorPlugin as MUMerchantHydratorPlugin;
use Pyz\Zed\MerchantManagement\Communication\Plugin\Merchant\BillingBranchInformationBranchPreSaverPlugin;
use Pyz\Zed\MerchantManagement\Communication\Plugin\Merchant\OrderOnTimeslotBranchPreSaverPlugin;
use Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant\SoftwareFeatureHydratorPlugin;
use Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant\SoftwareFeatureSaverPlugin;
use Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant\SoftwarePackageHydratorPlugin;
use Pyz\Zed\SoftwarePackage\Communication\Plugin\Merchant\SoftwarePackageSaverPlugin;
use Pyz\Zed\Touch\Communication\Plugin\Merchant\BranchPostRemoveTouchPlugin;
use Pyz\Zed\Touch\Communication\Plugin\Merchant\BranchPostSaveTouchPlugin;
use Pyz\Zed\Touch\Communication\Plugin\Merchant\PaymentMethodPostRemoveTouchPlugin;
use Pyz\Zed\Touch\Communication\Plugin\Merchant\PaymentMethodPostSaveTouchPlugin;
use Pyz\Zed\Touch\Communication\Plugin\Merchant\PaymentMethodToBranchPostRemoveTouchPlugin;
use Pyz\Zed\Touch\Communication\Plugin\Merchant\PaymentMethodToBranchPostSaveTouchPlugin;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class MerchantDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_SESSION = 'client session';

    public const SERVICE_DATE_FORMATTER = 'date formatter service';

    public const FACADE_MONEY = 'money facade';
    public const FACADE_PRODUCT = 'product facade';
    public const FACADE_LOCALE = 'locale facade';
    public const FACADE_CATEGORY = 'category facade';
    public const FACADE_PRODUCT_CATEGORY = 'product category facade';
    public const FACADE_DELIVERY_AREA = 'delivery area facade';
    public const FACADE_MERCHANT_PRICE = 'merchant price facade';
    public const FACADE_GRAPHMASTERS = 'graphmasters facade';

    public const QUERY_CONTAINER_PRODUCT = 'product query container';
    public const QUERY_CONTAINER_CATEGORY = 'category query container';
    public const QUERY_CONTAINER_DELIVERY_AREA = 'delivery area query container';
    public const QUERY_CONTAINER_MERCHANT_PRICE = 'merchant price query container';
    public const QUERY_CONTAINER_DEPOSIT = 'QUERY_CONTAINER_DEPOSIT';

    public const MERCHANT_HYDRATOR_PLUGINS = 'MERCHANT_HYDRATOR_PLUGINS';
    public const MERCHANT_SAVER_PLUGINS = 'MERCHANT_SAVER_PLUGINS';

    public const BRANCH_HYDRATOR_PLUGINS = 'BRANCH_HYDRATOR_PLUGINS';
    public const BRANCH_PRE_SAVER_PLUGINS = 'BRANCH_PRE_SAVER_PLUGINS';
    public const BRANCH_SAVER_PLUGINS = 'BRANCH_SAVER_PLUGINS';
    public const BRANCH_REMOVE_PLUGINS = 'BRANCH_REMOVE_PLUGINS';
    public const BRANCH_STATUS_INDEPENDENT_SAVE_PLUGINS = 'BRANCH_STATUS_INDEPENDENT_SAVE_PLUGINS';

    public const PAYMENT_METHOD_TO_BRANCH_POST_ADD_PLUGIN = 'PAYMENT_METHOD_TO_BRANCH_POST_ADD_PLUGIN';
    public const PAYMENT_METHOD_TO_BRANCH_POST_REMOVE_PLUGIN = 'PAYMENT_METHOD_TO_BRANCH_POST_REMOVE_PLUGIN';
    public const PAYMENT_METHOD_POST_SAVE_PLUGIN = 'PAYMENT_METHOD_POST_SAVE_PLUGIN';
    public const PAYMENT_METHOD_POST_REMOVE_PLUGIN = 'PAYMENT_METHOD_POST_REMOVE_PLUGIN';

    public const BRANCH_USER_HYDRATOR_PLUGINS = 'BRANCH_USER_HYDRATOR_PLUGINS';

    public const MERCHANT_USER_HYDRATOR_PLUGINS = 'MERCHANT_USER_HYDRATOR_PLUGINS';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addSession($container);
        $container = $this->addProductFacade($container);
        $container = $this->addDepositQueryContainer($container);
        $container = $this->addDeliveryAreaFacade($container);
        $container = $this->addMerchantHydratorPlugins($container);
        $container = $this->addMerchantSaverPlugins($container);
        $container = $this->addBranchHydratorPlugins($container);
        $container = $this->addBranchPreSaverPlugins($container);
        $container = $this->addBranchSaverPlugins($container);
        $container = $this->addBranchRemovePlugins($container);
        $container = $this->addBranchStatusIndependentSaverPlugins($container);
        $container = $this->addPaymentMethodPostSavePlugins($container);
        $container = $this->addPaymentMethodPostRemovePlugins($container);
        $container = $this->addPaymentMethodToBranchPostAddPlugins($container);
        $container = $this->addPaymentMethodToBranchPostRemovePlugins($container);
        $container = $this->addBranchUserHydratorPlugins($container);
        $container = $this->addMerchantUserHydratorPlugins($container);
        $container = $this->addGraphMastersFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addDateFormatter($container);
        $container = $this->addMoneyFacade($container);
        $container = $this->addProductFacade($container);
        $container = $this->addLocaleFacade($container);
        $container = $this->addProductQueryContainer($container);
        $container = $this->addCategoryQueryContainer($container);
        $container = $this->addCategoryFacade($container);
        $container = $this->addProductCategoryFacade($container);
        $container = $this->addDeliveryAreaQueryContainer($container);
        $container = $this->addDeliveryAreaFacade($container);
        $container = $this->addMerchantPriceQueryContainer($container);
        $container = $this->addMerchantPriceFacade($container);

        return $container;
    }


    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantHydratorPlugins(Container $container) : Container
    {
        $container[static::MERCHANT_HYDRATOR_PLUGINS] = function (Container $container) {
            return $this->getMerchantHydratorPlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantSaverPlugins(Container $container) : Container
    {
        $container[static::MERCHANT_SAVER_PLUGINS] = function (Container $container) {
            return $this->getMerchantSaverPlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addBranchHydratorPlugins(Container $container) : Container
    {
        $container[static::BRANCH_HYDRATOR_PLUGINS] = function (Container $container) {
            return $this->getBranchHydratorPlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addBranchPreSaverPlugins(Container $container) : Container
    {
        $container[static::BRANCH_PRE_SAVER_PLUGINS] = function (Container $container) {
            return $this->getBranchPreSaverPlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addBranchSaverPlugins(Container $container) : Container
    {
        $container[static::BRANCH_SAVER_PLUGINS] = function (Container $container) {
            return $this->getBranchSaverPlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addBranchRemovePlugins(Container $container) : Container
    {
        $container[static::BRANCH_REMOVE_PLUGINS] = function (Container $container) {
            return $this->getBranchRemovePlugins();
        };

        return $container;
    }

    protected function addBranchStatusIndependentSaverPlugins(Container $container) : Container
    {
        $container[static::BRANCH_STATUS_INDEPENDENT_SAVE_PLUGINS] = function (Container $container) {
            return $this->getBranchStatusIndependentSaverPlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPaymentMethodToBranchPostAddPlugins(Container $container) : Container
    {
        $container[static::PAYMENT_METHOD_TO_BRANCH_POST_ADD_PLUGIN] = function (Container $container) {
            return $this->getPaymentMethodToBranchPostAddPlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPaymentMethodToBranchPostRemovePlugins(Container $container) : Container
    {
        $container[static::PAYMENT_METHOD_TO_BRANCH_POST_REMOVE_PLUGIN] = function (Container $container) {
            return $this->getPaymentMethodToBranchPostRemovePlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPaymentMethodPostSavePlugins(Container $container) : Container
    {
        $container[static::PAYMENT_METHOD_POST_SAVE_PLUGIN] = function (Container $container) {
            return $this->getPaymentMethodPostSavePlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPaymentMethodPostRemovePlugins(Container $container) : Container
    {
        $container[static::PAYMENT_METHOD_POST_REMOVE_PLUGIN] = function (Container $container) {
            return $this->getPaymentMethodPostRemovePlugins();
        };

        return $container;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    protected function getMerchantHydratorPlugins(): array
    {
        return [
            new SoftwarePackageHydratorPlugin(),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    protected function getMerchantSaverPlugins(): array
    {
        return [
            new SoftwarePackageSaverPlugin(),
            new BillingMerchantSaverPlugin(),
            new AccountingLicenseMerchantSaverPlugin(),
            new RealaxDebitorMerchantSaverPlugin(),
        ];
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\BranchHydratorPluginInterface[]
     */
    protected function getBranchHydratorPlugins(): array
    {
        return [
            new SoftwareFeatureHydratorPlugin(),
        ];
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\BranchPostSaverPluginInterface[]
     */
    protected function getBranchPreSaverPlugins(): array
    {
        return [
            new BillingBranchPreSaverPlugin(),
            new CsvExportBranchPreSaverPlugin(),
            new OrderOnTimeslotBranchPreSaverPlugin(),
            new BillingBranchInformationBranchPreSaverPlugin(),
            new OffersDepositPickupBranchPreSaverPlugin(),
            new EdiExportVersionBranchPreSaverPlugin(),
            new EdiExcludeMissingItemReturnsBranchPreSaverPlugin(),
        ];
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\BranchPostSaverPluginInterface[]
     */
    protected function getBranchSaverPlugins(): array
    {
        return [
            new BranchPostSaveTouchPlugin(),
        ];
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\BranchPostRemovePluginInterface[]
     */
    protected function getBranchRemovePlugins(): array
    {
        return [
            new BranchPostRemoveTouchPlugin(),
        ];
    }

    /**
     * @return array
     */
    protected function getBranchStatusIndependentSaverPlugins(): array
    {
        return [
            new SoftwareFeatureSaverPlugin(),
        ];
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodToBranchPostSavePluginInterface[]
     */
    protected function getPaymentMethodToBranchPostAddPlugins() : array
    {
        return [
            new PaymentMethodToBranchPostSaveTouchPlugin(),
        ];
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodToBranchPostRemovePluginInterface[]
     */
    protected function getPaymentMethodToBranchPostRemovePlugins() : array
    {
        return [
            new PaymentMethodToBranchPostRemoveTouchPlugin(),
        ];
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodPostSavePluginInterface[]
     */
    protected function getPaymentMethodPostSavePlugins() : array
    {
        return [
            new PaymentMethodPostSaveTouchPlugin(),
        ];
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodPostRemovePluginInterface[]
     */
    protected function getPaymentMethodPostRemovePlugins() : array
    {
        return [
            new PaymentMethodPostRemoveTouchPlugin(),
        ];
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\BranchUserHydratorPluginInterface[]
     */
    protected function getBranchUserHydratorPlugins(): array
    {
        return [
            new BranchHydratorPlugin(),
            new MerchantHydratorPlugin(),
        ];
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\MerchantUserHydratorPluginInterface[]
     */
    protected function getMerchantUserHydratorPlugins(): array
    {
        return [
            new MUBranchHydratorPlugin(),
            new MUMerchantHydratorPlugin(),
        ];
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function addProductCategoryFacade(Container $container) : Container
    {
        $container[static::FACADE_PRODUCT_CATEGORY] = function (Container $container) {
            return $container->getLocator()->productCategory()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function addCategoryFacade(Container $container) : Container
    {
        $container[static::FACADE_CATEGORY] = function (Container $container) {
            return $container->getLocator()->category()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function addCategoryQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_CATEGORY] = function (Container $container) {
            return $container->getLocator()->category()->queryContainer();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function addLocaleFacade(Container $container) : Container
    {
        $container[static::FACADE_LOCALE] = function (Container $container) {
            return $container->getLocator()->locale()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function addProductQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_PRODUCT] = function (Container $container) {
            return $container->getLocator()->product()->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSession(Container $container) : Container
    {
        $container[static::CLIENT_SESSION] = function (Container $container) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $container->getLocator()->session()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDepositQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_DEPOSIT] = function (Container $container) {
            return $container->getLocator()->deposit()->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDateFormatter(Container $container) : Container
    {
        $container[static::SERVICE_DATE_FORMATTER] = function (Container $container) {
            return $container->getLocator()->utilDateTime()->service();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMoneyFacade(Container $container) : Container
    {
        $container[static::FACADE_MONEY] = function (Container $container) {
            return $container->getLocator()->money()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductFacade(Container $container) : Container
    {
        $container[static::FACADE_PRODUCT] = function (Container $container) {
            return $container->getLocator()->product()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDeliveryAreaQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_DELIVERY_AREA] = function (Container $container) {
            return $container->getLocator()->deliveryArea()->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDeliveryAreaFacade(Container $container) : Container
    {
        $container[static::FACADE_DELIVERY_AREA] = function (Container $container) {
            return $container->getLocator()->deliveryArea()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantPriceQueryContainer(Container $container) : Container
    {
        $container[static::QUERY_CONTAINER_MERCHANT_PRICE] = function (Container $container) {
            return $container->getLocator()->merchantPrice()->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantPriceFacade(Container $container) : Container
    {
        $container[static::FACADE_MERCHANT_PRICE] = function (Container $container) {
            return $container->getLocator()->merchantPrice()->facade();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addBranchUserHydratorPlugins(Container $container): Container
    {
        $container[static::BRANCH_USER_HYDRATOR_PLUGINS] = function (Container $container) {
            return $this
                ->getBranchUserHydratorPlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMerchantUserHydratorPlugins(Container $container): Container
    {
        $container[static::MERCHANT_USER_HYDRATOR_PLUGINS] = function (Container $container) {
            return $this
                ->getMerchantUserHydratorPlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addGraphMastersFacade(Container $container): Container
    {
        $container[static::FACADE_GRAPHMASTERS] = function (Container $container) {
            return $container->getLocator()->graphMasters()->facade();
        };

        return $container;
    }
}
