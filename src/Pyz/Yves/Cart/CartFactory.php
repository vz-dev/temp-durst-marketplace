<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\Cart;

use Pyz\Yves\Cart\Form\VoucherForm;
use Pyz\Yves\Cart\Handler\CartItemHandler;
use Pyz\Yves\Cart\Handler\CartOperationHandler;
use Pyz\Yves\Cart\Handler\ProductBundleCartOperationHandler;
use Pyz\Yves\Cart\Plugin\Provider\AttributeVariantsProvider;
use Pyz\Yves\Discount\Handler\VoucherHandler;
use Pyz\Yves\Product\Mapper\AttributeVariantMapper;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\ProductBundle\Grouper\ProductBundleGrouper;

class CartFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Client\Calculation\CalculationClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCalculationClient()
    {
        return $this->getProvidedDependency(CartDependencyProvider::CLIENT_CALCULATION);
    }

    /**
     * @return \Spryker\Client\Cart\CartClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCartClient()
    {
        return $this->getProvidedDependency(CartDependencyProvider::CLIENT_CART);
    }

    /**
     * @return VoucherHandler
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCartVoucherHandler()
    {
        return new VoucherHandler(
            $this->getCalculationClient(),
            $this->getCartClient(),
            $this->getFlashMessenger()
        );
    }

    /**
     * @return CartOperationHandler
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCartOperationHandler()
    {
        return new CartOperationHandler(
            $this->getCartClient(),
            $this->getLocale(),
            $this->getFlashMessenger(),
            $this->getRequest(),
            $this->getAvailabilityClient()
        );
    }

    /**
     * @return ProductBundleCartOperationHandler
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createProductBundleCartOperationHandler()
    {
        return new ProductBundleCartOperationHandler(
            $this->createCartOperationHandler(),
            $this->getCartClient(),
            $this->getLocale(),
            $this->getFlashMessenger()
        );
    }

    /**
     * @return CartItemHandler
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCartItemHandler()
    {
        return new CartItemHandler(
            $this->createCartOperationHandler(),
            $this->getCartClient(),
            $this->getProductClient(),
            $this->getStorageProductMapperPlugin(),
            $this->getFlashMessenger()
        );
    }

    /**
     * @return \Spryker\Yves\ProductBundle\Grouper\ProductBundleGrouper
     */
    public function createProductBundleGrouper()
    {
        return new ProductBundleGrouper();
    }

    /**
     * @return mixed
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getApplication()
    {
        return $this->getProvidedDependency(CartDependencyProvider::PLUGIN_APPLICATION);
    }

    /**
     * @return mixed
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getFlashMessenger()
    {
        return $this->getApplication()['flash_messenger'];
    }

    /**
     * @return mixed
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getLocale()
    {
        return $this->getApplication()['locale'];
    }

    /**
     * @return mixed
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getRequest()
    {
        return $this->getApplication()['request'];
    }

    /**
     * @return mixed
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getVoucherForm()
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY)
            ->create(VoucherForm::class);
    }

    /**
     * @return \Symfony\Component\Form\AbstractType
     */
    protected function createVoucherFormType()
    {
        return new VoucherForm();
    }

    /**
     * @return \Pyz\Yves\Checkout\Plugin\CheckoutBreadcrumbPlugin
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCheckoutBreadcrumbPlugin()
    {
        return $this->getProvidedDependency(CartDependencyProvider::PLUGIN_CHECKOUT_BREADCRUMB);
    }

    /**
     * @return \Spryker\Yves\CartVariant\Dependency\Plugin\CartVariantAttributeMapperPluginInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCartVariantAttributeMapperPlugin()
    {
        return $this->getProvidedDependency(CartDependencyProvider::PLUGIN_CART_VARIANT);
    }

    /**
     * @return AttributeVariantMapper
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createAttributeVariantMapper()
    {
        return new AttributeVariantMapper($this->getProductClient());
    }

    /**
     * @return \Pyz\Yves\Product\Dependency\Plugin\StorageProductMapperPluginInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getStorageProductMapperPlugin()
    {
        return $this->getProvidedDependency(CartDependencyProvider::PLUGIN_STORAGE_PRODUCT_MAPPER);
    }

    /**
     * @return AttributeVariantsProvider
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCartItemsAttributeProvider()
    {
        return new AttributeVariantsProvider(
            $this->getCartVariantAttributeMapperPlugin(),
            $this->createCartItemHandler()
        );
    }

    /**
     * @return \Spryker\Client\Product\ProductClientInterface $productClient
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getProductClient()
    {
        return $this->getProvidedDependency(CartDependencyProvider::CLIENT_PRODUCT);
    }

    /**
     * @return \Spryker\Yves\DiscountPromotion\Dependency\PromotionProductMapperPluginInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getProductPromotionMapperPlugin()
    {
        return $this->getProvidedDependency(CartDependencyProvider::PLUGIN_PROMOTION_PRODUCT_MAPPER);
    }

    /**
     * @return \Spryker\Client\Availability\AvailabilityClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getAvailabilityClient()
    {
        return $this->getProvidedDependency(CartDependencyProvider::CLIENT_AVAILABILITY);
    }
}
