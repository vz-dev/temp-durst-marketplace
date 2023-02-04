<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 19.11.18
 * Time: 10:08
 */

namespace Pyz\Zed\Merchant\Business;

use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\Merchant\Business\Code\CodeGenerator;
use Pyz\Zed\Merchant\Business\Code\CodeGeneratorInterface;
use Pyz\Zed\Merchant\Business\Code\GlnValidator;
use Pyz\Zed\Merchant\Business\Code\GlnValidatorInterface;
use Pyz\Zed\Merchant\Business\Model\Branch;
use Pyz\Zed\Merchant\Business\Model\BranchUser;
use Pyz\Zed\Merchant\Business\Model\BranchUserInterface;
use Pyz\Zed\Merchant\Business\Model\DepositSku;
use Pyz\Zed\Merchant\Business\Model\DepositSkuInterface;
use Pyz\Zed\Merchant\Business\Model\Merchant;
use Pyz\Zed\Merchant\Business\Checkout\BranchPaymentMethodChecker;
use Pyz\Zed\Merchant\Business\Checkout\BranchPaymentMethodCheckerInterface;
use Pyz\Zed\Merchant\Business\Map\BranchDataPageMapBuilder;
use Pyz\Zed\Merchant\Business\Map\PaymentProviderDataPageMapBuilder;
use Pyz\Zed\Merchant\Business\Model\MerchantUser;
use Pyz\Zed\Merchant\Business\Model\MerchantUserInterface;
use Pyz\Zed\Merchant\Business\Model\PaymentMethod;
use Pyz\Zed\Merchant\Business\Model\PaymentMethodInterface;
use Pyz\Zed\Merchant\Business\Model\Salutation;
use Pyz\Zed\Merchant\Business\Sales\OrderHydrator;
use Pyz\Zed\Merchant\Business\Sales\OrderHydratorInterface;
use Pyz\Zed\Merchant\Business\Validator\BranchValidator;
use Pyz\Zed\Merchant\MerchantConfig;
use Pyz\Zed\Merchant\MerchantDependencyProvider;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

/**
 * Class MerchantBusinessFactory
 * @package Pyz\Zed\Merchant\Business
 *
 * @method MerchantConfig getConfig()
 * @method MerchantQueryContainerInterface getQueryContainer()
 */
class MerchantBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Merchant\Business\Map\BranchDataPageMapBuilder
     */
    public function createBranchDataPageMapBuilder(): BranchDataPageMapBuilder
    {
        return new BranchDataPageMapBuilder();
    }

    /**
     * @return \Pyz\Zed\Merchant\Business\Map\PaymentProviderDataPageMapBuilder
     */
    public function createPaymentProviderDataPageMapBuilder(): PaymentProviderDataPageMapBuilder
    {
        return new PaymentProviderDataPageMapBuilder();
    }

    /**
     * @return \Pyz\Zed\Merchant\Business\Checkout\BranchPaymentMethodCheckerInterface
     */
    public function createBranchPaymentMethodChecker(): BranchPaymentMethodCheckerInterface
    {
        return new BranchPaymentMethodChecker(
            $this->getQueryContainer()
        );
    }

    /**
     *
     * @return \Pyz\Zed\Merchant\Business\Model\Merchant
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMerchantModel()
    {
        return new Merchant(
            $this->getQueryContainer(),
            $this->getSessionClient(),
            $this->getConfig(),
            $this->getMerchantHydratorPlugins(),
            $this->getMerchantSaverPlugins()
        );
    }

    /**
     * @return Branch
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createBranchModel(): Branch
    {
        return new Branch(
            $this->getQueryContainer(),
            $this->getSessionClient(),
            $this->getDeliveryAreaFacade(),
            $this->getConfig(),
            $this->createCodeGenerator(),
            $this->getBranchHydratorPlugins(),
            $this->getBranchSaverPlugins(),
            $this->getBranchPreSaverPlugins(),
            $this->getBranchRemovePlugins(),
            $this->getBranchStatusIndependentSaverPlugins(),
            $this->createGlnValidator(),
            $this->getGraphMastersFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Merchant\Business\Sales\OrderHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createOrderHydrator(): OrderHydratorInterface
    {
        return new OrderHydrator(
            $this->createBranchModel()
        );
    }

    /**
     * @return BranchValidator
     */
    public function createBranchValidator(): BranchValidator
    {
        return new BranchValidator(
            $this->getQueryContainer()
        );
    }

    /**
     * @return PaymentMethodInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPaymentMethodModel(): PaymentMethodInterface
    {
        return new PaymentMethod(
            $this->getQueryContainer(),
            $this->createMerchantModel(),
            $this->getPaymentMethodToBranchPostAddPlugins(),
            $this->getPaymentMethodToBranchPostRemovePlugins(),
            $this->getPaymentMethodPostSavePlugins(),
            $this->getPaymentMethodPostRemovePlugins()
        );
    }

    /**
     * @return Salutation
     */
    public function createSalutationModel(): Salutation
    {
        return new Salutation($this->getQueryContainer());
    }

    /**
     * @return GlnValidatorInterface
     */
    public function createGlnValidator(): GlnValidatorInterface
    {
        return new GlnValidator();
    }

    /**
     * @return \Pyz\Zed\Merchant\Business\Model\DepositSkuInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createDepositSkuModel(): DepositSkuInterface
    {
        return new DepositSku(
            $this->getQueryContainer(),
            $this->getDepositQueryContainer()
        );
    }

    /**
     * @return BranchUserInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createBranchUserModel(): BranchUserInterface
    {
        return new BranchUser(
            $this->getQueryContainer(),
            $this->getSessionClient(),
            $this->getConfig(),
            $this->getBranchUserHydratorPlugins()
        );
    }

    /**
     * @return MerchantUserInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMerchantUserModel(): MerchantUserInterface
    {
        return new MerchantUser(
            $this->getQueryContainer(),
            $this->getSessionClient(),
            $this->getConfig(),
            $this->getMerchantUserHydratorPlugins()
        );
    }

    /**
     * @return \Spryker\Client\Session\SessionClientInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSessionClient()
    {
        return $this->getProvidedDependency(MerchantDependencyProvider::CLIENT_SESSION);
    }

    /**
     * @return \Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDeliveryAreaFacade(): DeliveryAreaFacadeInterface
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::FACADE_DELIVERY_AREA);
    }

    /**
     * @return CodeGeneratorInterface
     */
    protected function createCodeGenerator(): CodeGeneratorInterface
    {
        return new CodeGenerator($this->getQueryContainer());
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\MerchantHydratorPluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantHydratorPlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::MERCHANT_HYDRATOR_PLUGINS);
    }

    /**
     * @return array
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantSaverPlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::MERCHANT_SAVER_PLUGINS);
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\BranchHydratorPluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getBranchHydratorPlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::BRANCH_HYDRATOR_PLUGINS);
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\BranchPreSaverPluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getBranchPreSaverPlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::BRANCH_PRE_SAVER_PLUGINS);
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\BranchPostSaverPluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getBranchSaverPlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::BRANCH_SAVER_PLUGINS);
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\BranchPostRemovePluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getBranchRemovePlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::BRANCH_REMOVE_PLUGINS);
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\BranchPostSaverPluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getBranchStatusIndependentSaverPlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::BRANCH_STATUS_INDEPENDENT_SAVE_PLUGINS);
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodToBranchPostSavePluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getPaymentMethodToBranchPostAddPlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::PAYMENT_METHOD_TO_BRANCH_POST_ADD_PLUGIN);
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodToBranchPostRemovePluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getPaymentMethodToBranchPostRemovePlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::PAYMENT_METHOD_TO_BRANCH_POST_REMOVE_PLUGIN);
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodPostSavePluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getPaymentMethodPostSavePlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::PAYMENT_METHOD_POST_SAVE_PLUGIN);
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\PaymentMethodPostRemovePluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getPaymentMethodPostRemovePlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::PAYMENT_METHOD_POST_REMOVE_PLUGIN);
    }

    /**
     * @return \Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDepositQueryContainer(): DepositQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::QUERY_CONTAINER_DEPOSIT);
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\BranchUserHydratorPluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getBranchUserHydratorPlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::BRANCH_USER_HYDRATOR_PLUGINS);
    }

    /**
     * @return \Pyz\Zed\Merchant\Communication\Plugin\MerchantUserHydratorPluginInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantUserHydratorPlugins(): array
    {
        return $this
            ->getProvidedDependency(MerchantDependencyProvider::MERCHANT_USER_HYDRATOR_PLUGINS);
    }

    /**
     * @return \Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getGraphMastersFacade(): GraphMastersFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                MerchantDependencyProvider::FACADE_GRAPHMASTERS
            );
    }
}
