<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\Wishlist;

use Generated\Shared\Transfer\WishlistTransfer;
use Pyz\Yves\Wishlist\Business\AvailabilityReader;
use Pyz\Yves\Wishlist\Business\MoveToCartHandler;
use Pyz\Yves\Wishlist\Form\AddAllAvailableProductsToCartFormType;
use Pyz\Yves\Wishlist\Form\DataProvider\AddAllAvailableProductsToCartFormDataProvider;
use Pyz\Yves\Wishlist\Form\DataProvider\WishlistFormDataProvider;
use Pyz\Yves\Wishlist\Form\WishlistFormType;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\Kernel\AbstractFactory;

/**
 * @method \Spryker\Client\Wishlist\WishlistClientInterface getClient()
 */
class WishlistFactory extends AbstractFactory
{
    /**
     * @return mixed
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCustomerClient()
    {
        return $this->getProvidedDependency(WishlistDependencyProvider::CLIENT_CUSTOMER);
    }

    /**
     * @param WishlistTransfer|null $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getWishlistForm(WishlistTransfer $data = null, array $options = [])
    {
        return $this->getFormFactory()->create($this->createWishlistFormType(), $data, $options);
    }

    /**
     * @return WishlistFormDataProvider
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createWishlistFormDataProvider()
    {
        return new WishlistFormDataProvider(
            $this->getClient(),
            $this->getCustomerClient()
        );
    }

    /**
     * @param array $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getAddAllAvailableProductsToCartForm(array $data, array $options = [])
    {
        return $this->getFormFactory()->create($this->createAddAllAvailableProductsToCartFormType(), $data, $options);
    }

    /**
     * @return \Pyz\Yves\Wishlist\Form\DataProvider\AddAllAvailableProductsToCartFormDataProvider
     */
    public function createAddAllAvailableProductsToCartFormDataProvider()
    {
        return new AddAllAvailableProductsToCartFormDataProvider();
    }

    /**
     * @return string
     */
    protected function createWishlistFormType()
    {
        return WishlistFormType::class;
    }

    /**
     * @return string
     */
    protected function createAddAllAvailableProductsToCartFormType()
    {
        return AddAllAvailableProductsToCartFormType::class;
    }

    /**
     * @return mixed
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getFormFactory()
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY);
    }

    /**
     * @return \Spryker\Client\Availability\AvailabilityClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getAvailabilityClient()
    {
        return $this->getProvidedDependency(WishlistDependencyProvider::CLIENT_AVAILABILITY);
    }

    /**
     * @return MoveToCartHandler
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMoveToCartHandler()
    {
        return new MoveToCartHandler($this->getClient(), $this->getCustomerClient(), $this->createAvailabilityReader());
    }

    /**
     * @return AvailabilityReader
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createAvailabilityReader()
    {
        return new AvailabilityReader($this->getAvailabilityClient());
    }
}
