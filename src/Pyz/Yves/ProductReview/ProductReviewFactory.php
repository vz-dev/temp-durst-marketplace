<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\ProductReview;

use Pyz\Yves\ProductReview\Controller\Calculator\ProductReviewSummaryCalculator;
use Pyz\Yves\ProductReview\Form\DataProvider\ProductReviewFormDataProvider;
use Pyz\Yves\ProductReview\Form\ProductReviewForm;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\ProductReview\ProductReviewFactory as SprykerProductReviewFactory;

class ProductReviewFactory extends SprykerProductReviewFactory
{
    /**
     * @return \Pyz\Client\Customer\CustomerClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCustomerClient()
    {
        return $this->getProvidedDependency(ProductReviewDependencyProvider::CLIENT_CUSTOMER);
    }

    /**
     * @return \Spryker\Client\Product\ProductClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getProductClient()
    {
        return $this->getProvidedDependency(ProductReviewDependencyProvider::CLIENT_PRODUCT);
    }

    /**
     * @return \Spryker\Client\ProductReview\ProductReviewClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getProductReviewClient()
    {
        return $this->getProvidedDependency(ProductReviewDependencyProvider::CLIENT_PRODUCT_REVIEW);
    }

    /**
     * @return \Symfony\Component\Form\FormFactory
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getFormFactory()
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY);
    }

    /**
     * @param $idProductAbstract
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createProductReviewForm($idProductAbstract)
    {
        $dataProvier = $this->createProductReviewFormDataProvider();
        $form = $this->getFormFactory()->create(
            ProductReviewForm::class,
            $dataProvier->getData($idProductAbstract),
            $dataProvier->getOptions()
        );

        return $form;
    }

    /**
     * @return ProductReviewSummaryCalculator
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createProductReviewSummaryCalculator()
    {
        return new ProductReviewSummaryCalculator($this->getProductReviewClient());
    }

    /**
     * @return \Pyz\Yves\ProductReview\Form\DataProvider\ProductReviewFormDataProvider
     */
    protected function createProductReviewFormDataProvider()
    {
        return new ProductReviewFormDataProvider();
    }
}
