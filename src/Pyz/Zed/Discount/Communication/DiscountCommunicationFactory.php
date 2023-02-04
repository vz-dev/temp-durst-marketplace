<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-05
 * Time: 14:38
 */

namespace Pyz\Zed\Discount\Communication;

use Generated\Shared\Transfer\DiscountConfiguratorTransfer;
use Pyz\Zed\Discount\Communication\Form\DataProvider\DiscountFormDataProvider;
use Pyz\Zed\Discount\Communication\Form\DiscountForm;
use Pyz\Zed\Discount\Dependency\Facade\DiscountToCalculationBridgeInterface;
use Pyz\Zed\Discount\Communication\Form\VoucherForm;
use Pyz\Zed\Discount\Dependency\Facade\DiscountToTaxBridgeInterface;
use Pyz\Zed\Discount\DiscountDependencyProvider;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Spryker\Zed\Discount\Communication\DiscountCommunicationFactory as SprykerDiscountCommunicationFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Money\Communication\Form\DataProvider\MoneyCollectionSingleStoreDataProvider;
use Spryker\Zed\Money\Communication\Form\DataProvider\MoneyDataProvider;
use Spryker\Zed\Money\Dependency\Facade\MoneyToCurrencyInterface;
use Spryker\Zed\Money\Dependency\Facade\MoneyToStoreInterface;
use Symfony\Component\Form\FormInterface;

class DiscountCommunicationFactory extends SprykerDiscountCommunicationFactory
{

    /**
     * @return MoneyCollectionSingleStoreDataProvider
     * @throws ContainerKeyNotFoundException
     */
    public function createMoneyCollectionSingleStoreDataProvider(): MoneyCollectionSingleStoreDataProvider
    {
        return new MoneyCollectionSingleStoreDataProvider(
            $this
            ->getMoneyCurrencyFacade()
        );
    }

    /**
     * @return MoneyDataProvider
     * @throws ContainerKeyNotFoundException
     */
    public function createMoneyDataProvider(): MoneyDataProvider
    {
        return new MoneyDataProvider(
            $this
            ->getStoreFacade()
        );
    }

    /**
     * @return MoneyToCurrencyInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getMoneyCurrencyFacade(): MoneyToCurrencyInterface
    {
        return $this
            ->getProvidedDependency(DiscountDependencyProvider::FACADE_MONEY_CURRENCY);
    }

    /**
     * @return MoneyToStoreInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getStoreFacade(): MoneyToStoreInterface
    {
        return $this
            ->getProvidedDependency(DiscountDependencyProvider::FACADE_STORE);
    }

    /**
     * @return MerchantQueryContainerInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getMerchantQueryContainer(): MerchantQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(DiscountDependencyProvider::QUERY_CONTAINER_MERCHANT);
    }

    /**
     * @return \Pyz\Zed\Discount\Dependency\Facade\DiscountToTaxBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getTaxFacade(): DiscountToTaxBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                DiscountDependencyProvider::FACADE_TAX
            );
    }

    /**
     * @return \Pyz\Zed\Discount\Dependency\Facade\DiscountToCalculationBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCalculationFacade(): DiscountToCalculationBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                DiscountDependencyProvider::FACADE_CALCULATION
            );
    }

    /**
     * @param int|null $idDiscount
     * @return FormInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createDiscountForm($idDiscount = null): FormInterface
    {
        $discountDataProvider = $this
            ->createDiscountDataProvider();

        $defaultOptions = [
            'data_class' => DiscountConfiguratorTransfer::class
        ];

        return $this
            ->getFormFactory()
            ->create(
                DiscountForm::class,
                $discountDataProvider->getData($idDiscount),
                array_merge($defaultOptions, $discountDataProvider->getOptions())
            );
    }

    /**
     * @return DiscountFormDataProvider
     * @throws ContainerKeyNotFoundException
     */
    protected function createDiscountDataProvider(): DiscountFormDataProvider
    {
        $discountFormDataProvider = new DiscountFormDataProvider(
            $this->getMerchantQueryContainer(),
            $this->getCurrencyFacade()
        );

        $discountFormDataProvider
            ->applyFormDataExpanderPlugins(
                $this->getDiscountFormDataProviderExpanderPlugins()
            );

        return $discountFormDataProvider;
    }

    /**
     * @return string
     */
    public function createVoucherFormType()
    {
        return VoucherForm::class;
    }
}
