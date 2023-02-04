<?php
/**
 * Durst - project - CampaignBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 12:03
 */

namespace Pyz\Zed\Campaign\Business;

use Pyz\Zed\Campaign\Business\Hydrator\CampaignAdvertisingMaterial\CampaignAdvertisingMaterialDaysAndEndDateHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignAdvertisingMaterial\CampaignAdvertisingMaterialHydratorInterface;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod\CampaignPeriodBookableHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod\CampaignPeriodCampaignAdvertisingMaterialHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod\CampaignPeriodDaysAndStartHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod\CampaignPeriodHydratorInterface;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder\CampaignPeriodBranchOrderBranchHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder\CampaignPeriodBranchOrderCampaignPeriodHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder\CampaignPeriodBranchOrderHydratorInterface;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder\CampaignPeriodBranchOrderProductsHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductAdvertisingMaterialHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductBranchHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductDiscountHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductHydratorInterface;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductImagesHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductProductConcreteHydrator;
use Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductProductInformationHydrator;
use Pyz\Zed\Campaign\Business\Model\CampaignAdvertisingMaterial;
use Pyz\Zed\Campaign\Business\Model\CampaignAdvertisingMaterialInterface;
use Pyz\Zed\Campaign\Business\Model\CampaignPeriod;
use Pyz\Zed\Campaign\Business\Model\CampaignPeriodBranchOrder;
use Pyz\Zed\Campaign\Business\Model\CampaignPeriodBranchOrderInterface;
use Pyz\Zed\Campaign\Business\Model\CampaignPeriodBranchOrderProduct;
use Pyz\Zed\Campaign\Business\Model\CampaignPeriodInterface;
use Pyz\Zed\Campaign\Business\Model\MerchantCampaignOrder;
use Pyz\Zed\Campaign\Business\Model\MerchantCampaignOrderInterface;
use Pyz\Zed\Campaign\Business\Model\Product;
use Pyz\Zed\Campaign\Business\Model\ProductInterface;
use Pyz\Zed\Campaign\Business\Utility\ImageUtil;
use Pyz\Zed\Campaign\Business\Utility\ImageUtilInterface;
use Pyz\Zed\Campaign\Business\Validator\CampaignPeriod\CampaignPeriodDateValidator;
use Pyz\Zed\Campaign\Business\Validator\CampaignPeriod\CampaignPeriodValidatorInterface;
use Pyz\Zed\Campaign\CampaignConfig;
use Pyz\Zed\Campaign\CampaignDependencyProvider;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface;
use Pyz\Zed\Product\Business\ProductFacadeInterface;
use Pyz\Zed\Product\Persistence\ProductQueryContainerInterface;
use Spryker\Service\UtilEncoding\UtilEncodingServiceInterface;
use Spryker\Zed\Currency\Business\CurrencyFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;

/**
 * Class CampaignBusinessFactory
 * @package Pyz\Zed\Campaign\Business
 * @method CampaignQueryContainer getQueryContainer()
 * @method CampaignConfig getConfig()
 */
class CampaignBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Campaign\Business\Model\CampaignPeriodInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCampaignPeriodModel(): CampaignPeriodInterface
    {
        return new CampaignPeriod(
            $this
                ->getQueryContainer(),
            $this
                ->createCampaignPeriodHydrators()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Model\CampaignAdvertisingMaterialInterface
     */
    public function createCampaignAdvertisingMaterialModel(): CampaignAdvertisingMaterialInterface
    {
        return new CampaignAdvertisingMaterial(
            $this
                ->getQueryContainer(),
            $this
                ->createCampaignAdvertisingMaterialHydrators()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Model\CampaignPeriodBranchOrderInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCampaignPeriodBranchOrderModel(): CampaignPeriodBranchOrderInterface
    {
        return new CampaignPeriodBranchOrder(
            $this
                ->getQueryContainer(),
            $this
                ->getCampaignFacade(),
            $this
                ->getDiscountFacade(),
            $this
                ->getConfig(),
            $this
                ->createCampaignPeriodBranchOrderHydrators()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Model\CampaignPeriodBranchOrderProduct
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCampaignPeriodBranchOrderProductModel(): CampaignPeriodBranchOrderProduct
    {
        return new CampaignPeriodBranchOrderProduct(
            $this
                ->getQueryContainer(),
            $this
                ->createCampaignPeriodBranchOrderProductHydrators()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Model\MerchantCampaignOrderInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMerchantCampaignOrderModel(): MerchantCampaignOrderInterface
    {
        return new MerchantCampaignOrder(
            $this
                ->getQueryContainer(),
            $this
                ->getCampaignFacade(),
            $this
                ->getDiscountFacade(),
            $this
                ->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Model\ProductInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createProductModel(): ProductInterface
    {
        return new Product(
            $this
                ->getCampaignFacade(),
            $this
                ->getMerchantPriceFacade(),
            $this
                ->getDiscountFacade(),
            $this
                ->getMoneyFacade(),
            $this
                ->getCurrencyFacade(),
            $this
                ->getProductQueryContainer(),
            $this
                ->getDiscountQueryContainer(),
            $this
                ->createImageUtility(),
            $this
                ->getUtilEncodingService()
        );
    }

    /**
     * @return array|CampaignPeriodHydratorInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCampaignPeriodHydrators(): array
    {
        return [
            $this
                ->createCampaignPeriodDaysAndStartHydrator(),
            $this
                ->createCampaignPeriodCampaignAdvertisingMaterialHydrator(),
            $this
                ->createCampaignPeriodBookableHydrator()
        ];
    }

    /**
     * @return array|\Pyz\Zed\Campaign\Business\Hydrator\CampaignAdvertisingMaterial\CampaignAdvertisingMaterialHydratorInterface[]
     */
    public function createCampaignAdvertisingMaterialHydrators(): array
    {
        return [
            $this
                ->createCampaignAdvertisingMaterialDaysAndEndDateHydrator()
        ];
    }

    /**
     * @return array|\Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder\CampaignPeriodBranchOrderHydratorInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCampaignPeriodBranchOrderHydrators(): array
    {
        return [
            $this
                ->createCampaignPeriodBranchOrderBranchHydrator(),
            $this
                ->createCampaignPeriodBranchOrderCampaignPeriodHydrator(),
            $this
                ->createCampaignPeriodBranchOrderProductsHydrator()
        ];
    }

    /**
     * @return array|\Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductHydratorInterface]|
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCampaignPeriodBranchOrderProductHydrators(): array
    {
        return [
            $this
                ->createCampaignPeriodBranchOrderProductProductConcreteHydrator(),
            $this
                ->createCampaignPeriodBranchOrderProductDiscountHydrator(),
            $this
                ->createCampaignPeriodBranchOrderProductBranchHydrator(),
            $this
                ->createCampaignPeriodBranchOrderProductAdvertisingMaterialHydrator(),
            $this
                ->createCampaignPeriodBranchOrderProductProductInformationHydrator(),
            $this
                ->createCampaignPeriodBranchOrderProductImagesHydrator()
        ];
    }

    /**
     * @return array|\Pyz\Zed\Campaign\Business\Validator\CampaignPeriod\CampaignPeriodValidatorInterface[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCampaignPeriodValidators(): array
    {
        return [
            $this
                ->createCampaignPeriodDateValidator()
        ];
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Utility\ImageUtilInterface
     */
    public function createImageUtility(): ImageUtilInterface
    {
        return new ImageUtil(
            $this
                ->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod\CampaignPeriodHydratorInterface
     */
    protected function createCampaignPeriodDaysAndStartHydrator(): CampaignPeriodHydratorInterface
    {
        return new CampaignPeriodDaysAndStartHydrator();
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod\CampaignPeriodHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCampaignPeriodCampaignAdvertisingMaterialHydrator(): CampaignPeriodHydratorInterface
    {
        return new CampaignPeriodCampaignAdvertisingMaterialHydrator(
            $this
                ->getCampaignFacade(),
            $this
                ->getQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriod\CampaignPeriodHydratorInterface
     */
    protected function createCampaignPeriodBookableHydrator(): CampaignPeriodHydratorInterface
    {
        return new CampaignPeriodBookableHydrator();
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignAdvertisingMaterial\CampaignAdvertisingMaterialHydratorInterface
     */
    protected function createCampaignAdvertisingMaterialDaysAndEndDateHydrator(): CampaignAdvertisingMaterialHydratorInterface
    {
        return new CampaignAdvertisingMaterialDaysAndEndDateHydrator(
            $this
                ->getQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder\CampaignPeriodBranchOrderHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCampaignPeriodBranchOrderBranchHydrator(): CampaignPeriodBranchOrderHydratorInterface
    {
        return new CampaignPeriodBranchOrderBranchHydrator(
            $this
                ->getMerchantFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder\CampaignPeriodBranchOrderHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCampaignPeriodBranchOrderCampaignPeriodHydrator(): CampaignPeriodBranchOrderHydratorInterface
    {
        return new CampaignPeriodBranchOrderCampaignPeriodHydrator(
            $this
                ->getCampaignFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrder\CampaignPeriodBranchOrderHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCampaignPeriodBranchOrderProductsHydrator(): CampaignPeriodBranchOrderHydratorInterface
    {
        return new CampaignPeriodBranchOrderProductsHydrator(
            $this
                ->getCampaignFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCampaignPeriodBranchOrderProductProductConcreteHydrator(): CampaignPeriodBranchOrderProductHydratorInterface
    {
        return new CampaignPeriodBranchOrderProductProductConcreteHydrator(
            $this
                ->getProductFacade(),
            $this
                ->getUtilEncodingService()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCampaignPeriodBranchOrderProductDiscountHydrator(): CampaignPeriodBranchOrderProductHydratorInterface
    {
        return new CampaignPeriodBranchOrderProductDiscountHydrator(
            $this
                ->getDiscountFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCampaignPeriodBranchOrderProductBranchHydrator(): CampaignPeriodBranchOrderProductHydratorInterface
    {
        return new CampaignPeriodBranchOrderProductBranchHydrator(
            $this
                ->getMerchantFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCampaignPeriodBranchOrderProductAdvertisingMaterialHydrator(): CampaignPeriodBranchOrderProductHydratorInterface
    {
        return new CampaignPeriodBranchOrderProductAdvertisingMaterialHydrator(
            $this
                ->getCampaignFacade(),
            $this
                ->getQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCampaignPeriodBranchOrderProductProductInformationHydrator(): CampaignPeriodBranchOrderProductHydratorInterface
    {
        return new CampaignPeriodBranchOrderProductProductInformationHydrator(
            $this
                ->getMerchantPriceFacade(),
            $this
                ->getDiscountFacade(),
            $this
                ->createImageUtility(),
            $this
                ->getMoneyFacade(),
            $this
                ->getCurrencyFacade(),
            $this
                ->getConfig(),
            $this
                ->getDepositFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct\CampaignPeriodBranchOrderProductHydratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCampaignPeriodBranchOrderProductImagesHydrator(): CampaignPeriodBranchOrderProductHydratorInterface
    {
        return new CampaignPeriodBranchOrderProductImagesHydrator(
            $this
                ->createImageUtility(),
            $this
                ->getUtilEncodingService()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\Validator\CampaignPeriod\CampaignPeriodValidatorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCampaignPeriodDateValidator(): CampaignPeriodValidatorInterface
    {
        return new CampaignPeriodDateValidator(
            $this
                ->getQueryContainer(),
            $this
                ->getCampaignFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Business\CampaignFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCampaignFacade(): CampaignFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CampaignDependencyProvider::FACADE_CAMPAIGN
            );
    }

    /**
     * @return \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CampaignDependencyProvider::FACADE_MERCHANT
            );
    }

    /**
     * @return \Pyz\Zed\Product\Business\ProductFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getProductFacade(): ProductFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CampaignDependencyProvider::FACADE_PRODUCT
            );
    }

    /**
     * @return \Pyz\Zed\Discount\Business\DiscountFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDiscountFacade(): DiscountFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CampaignDependencyProvider::FACADE_DISCOUNT
            );
    }

    /**
     * @return \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantPriceFacade(): MerchantPriceFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CampaignDependencyProvider::FACADE_MERCHANT_PRICE
            );
    }

    /**
     * @return \Spryker\Zed\Money\Business\MoneyFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMoneyFacade(): MoneyFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CampaignDependencyProvider::FACADE_MONEY
            );
    }

    /**
     * @return \Spryker\Zed\Currency\Business\CurrencyFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCurrencyFacade(): CurrencyFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CampaignDependencyProvider::FACADE_CURRENCY
            );
    }

    /**
     * @return \Pyz\Zed\Deposit\Business\DepositFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDepositFacade(): DepositFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CampaignDependencyProvider::FACADE_DEPOSIT
            );
    }

    /**
     * @return \Pyz\Zed\Product\Persistence\ProductQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getProductQueryContainer(): ProductQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                CampaignDependencyProvider::PRODUCT_QUERY_CONTAINER
            );
    }

    /**
     * @return \Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDiscountQueryContainer(): DiscountQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                CampaignDependencyProvider::DISCOUNT_QUERY_CONTAINER
            );
    }

    /**
     * @return \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getUtilEncodingService(): UtilEncodingServiceInterface
    {
        return $this
            ->getProvidedDependency(
                CampaignDependencyProvider::SERVICE_UTIL_ENCODING
            );
    }
}
