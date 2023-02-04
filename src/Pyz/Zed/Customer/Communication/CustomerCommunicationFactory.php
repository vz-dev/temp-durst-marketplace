<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Customer\Communication;

use Pyz\Zed\Customer\Communication\Form\CustomerForm;
use Pyz\Zed\Customer\Communication\Form\CustomerUpdateForm;
use Pyz\Zed\Customer\CustomerDependencyProvider;
use Pyz\Zed\Customer\Dependency\Facade\CustomerToIntegraInterface;
use Spryker\Zed\Customer\Communication\CustomerCommunicationFactory as SprykerCustomerCommunicationFactory;
use Spryker\Zed\Newsletter\Business\NewsletterFacade;
use Spryker\Zed\Sales\Business\SalesFacade;
use Symfony\Component\Form\FormInterface;

class CustomerCommunicationFactory extends SprykerCustomerCommunicationFactory
{
    /**
     * @return SalesFacade
     */
    public function getSalesFacade()
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::SALES_FACADE);
    }

    /**
     * @return NewsletterFacade
     */
    public function getNewsletterFacade()
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::NEWSLETTER_FACADE);
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCustomerForm(array $data = [], array $options = []): FormInterface
    {
        return $this
            ->getFormFactory()
            ->create(
                CustomerForm::class,
                $data,
                $options
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCustomerUpdateForm(array $data = [], array $options = []): FormInterface
    {
        return $this
            ->getFormFactory()
            ->create(
                CustomerUpdateForm::class,
                $data,
                $options
            );
    }
}
