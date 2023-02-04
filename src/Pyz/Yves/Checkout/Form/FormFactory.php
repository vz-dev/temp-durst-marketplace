<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\Checkout\Form;

use Pyz\Client\Merchant\MerchantClientInterface;
use Pyz\Yves\Checkout\CheckoutDependencyProvider;
use Pyz\Yves\Checkout\Form\DataProvider\BranchFormDataProvider;
use Pyz\Yves\Checkout\Form\DataProvider\ConcreteTimeSlotFormDataProvider;
use Pyz\Yves\Checkout\Form\DataProvider\SubFormDataProviders;
use Pyz\Yves\Checkout\Form\Steps\BranchForm;
use Pyz\Yves\Checkout\Form\Steps\ConcreteTimeSlotForm;
use Pyz\Yves\Checkout\Form\Steps\PaymentForm;
use Pyz\Yves\Checkout\Form\Steps\SummaryForm;
use Pyz\Yves\Checkout\Form\Voucher\VoucherForm;
use Pyz\Yves\Customer\Form\CheckoutAddressCollectionForm;
use Pyz\Yves\Customer\Form\CustomerCheckoutForm;
use Pyz\Yves\Customer\Form\DataProvider\CheckoutAddressFormDataProvider;
use Pyz\Yves\Customer\Form\GuestForm;
use Pyz\Yves\Customer\Form\LoginForm;
use Pyz\Yves\Customer\Form\RegisterForm;
use Pyz\Yves\Shipment\Form\ShipmentForm;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\Checkout\Form\FormFactory as SprykerFormFactory;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection;
use Spryker\Yves\StepEngine\Form\FormCollectionHandler;
use Symfony\Component\Form\FormTypeInterface;

class FormFactory extends SprykerFormFactory
{
    /**
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCustomerFormCollection()
    {
        return $this->createFormCollection($this->getCustomerFormTypes());
    }

    /**
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createAddressFormCollection()
    {
        return $this->createFormCollection($this->getAddressFormTypes(), $this->createAddressFormDataProvider());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer
     *
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createShipmentFormCollection()
    {
        return $this->createFormCollection($this->getShipmentFormTypes(), $this->getShipmentFormDataProviderPlugin());
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    protected function getShipmentFormTypes()
    {
        return [
            $this->createShipmentForm(),
        ];
    }

    /**
     * @return \Pyz\Yves\Shipment\Form\ShipmentForm
     */
    protected function createShipmentForm()
    {
        return new ShipmentForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getShipmentFormDataProviderPlugin()
    {
        return $this->getProvidedDependency(CheckoutDependencyProvider::PLUGIN_SHIPMENT_FORM_DATA_PROVIDER);
    }

    /**
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPaymentFormCollection()
    {
        $createPaymentSubForms = $this->createPaymentMethodSubForms();
        $paymentFormType = $this->createPaymentForm($createPaymentSubForms);
        $subFormDataProvider = $this->createSubFormDataProvider($createPaymentSubForms);

        return $this->createSubFormCollection($paymentFormType, $subFormDataProvider);
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection $subForms
     *
     * @return \Pyz\Yves\Checkout\Form\DataProvider\SubFormDataProviders
     */
    protected function createSubFormDataProvider(SubFormPluginCollection $subForms)
    {
        return new SubFormDataProviders($subForms);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer
     *
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createSummaryFormCollection()
    {
        return $this->createFormCollection($this->createSummaryFormTypes());
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getVoucherForm()
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY)
            ->create($this->createVoucherFormType());
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCustomerFormTypes()
    {
        return [
            $this->createLoginForm(),
            $this->createCustomerCheckoutForm($this->createRegisterForm()),
            $this->createCustomerCheckoutForm($this->createGuestForm()),
        ];
    }

    /**
     * @param \Symfony\Component\Form\FormTypeInterface $subForm
     *
     * @return \Pyz\Yves\Customer\Form\CustomerCheckoutForm
     */
    protected function createCustomerCheckoutForm(FormTypeInterface $subForm)
    {
        return new CustomerCheckoutForm($subForm);
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    protected function getAddressFormTypes()
    {
        return [
            $this->createCheckoutAddressCollectionForm(),
        ];
    }

    /**
     * @return \Pyz\Yves\Customer\Form\CheckoutAddressCollectionForm
     */
    protected function createCheckoutAddressCollectionForm()
    {
        return new CheckoutAddressCollectionForm();
    }

    /**
     * @return \Pyz\Yves\Customer\Form\DataProvider\CheckoutAddressFormDataProvider
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createAddressFormDataProvider()
    {
        return new CheckoutAddressFormDataProvider($this->getCustomerClient(), $this->getStore());
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    protected function createSummaryFormTypes()
    {
        return [
            $this->createSummaryForm(),
            $this->createVoucherFormType(),
        ];
    }

    /**
     * @return \Pyz\Yves\Checkout\Form\Voucher\VoucherForm
     */
    protected function createVoucherFormType()
    {
        return new VoucherForm();
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection $subForms
     *
     * @return \Pyz\Yves\Checkout\Form\Steps\PaymentForm
     */
    protected function createPaymentForm(SubFormPluginCollection $subForms)
    {
        return new PaymentForm($subForms);
    }

    /**
     * @return \Pyz\Yves\Checkout\Form\Steps\SummaryForm
     */
    protected function createSummaryForm()
    {
        return new SummaryForm();
    }

    /**
     * @param \Symfony\Component\Form\FormTypeInterface[] $formTypes
     * @param \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface|null $dataProvider
     *
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createFormCollection(array $formTypes, StepEngineFormDataProviderInterface $dataProvider = null)
    {
        return new FormCollectionHandler($formTypes, $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY), $dataProvider);
    }

    /**
     * @param \Symfony\Component\Form\FormTypeInterface $formType
     * @param \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface $dataProvider
     *
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createSubFormCollection($formType, StepEngineFormDataProviderInterface $dataProvider)
    {
        return new FormCollectionHandler([$formType], $this->getFormFactory(), $dataProvider);
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getFormFactory()
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY);
    }

    /**
     * @return \Spryker\Yves\Kernel\Application
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getApplication()
    {
        return $this->getProvidedDependency(CheckoutDependencyProvider::PLUGIN_APPLICATION);
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getStore()
    {
        return $this->getProvidedDependency(CheckoutDependencyProvider::STORE);
    }

    /**
     * @return \Pyz\Client\Customer\CustomerClient
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCustomerClient()
    {
        return $this->getProvidedDependency(CheckoutDependencyProvider::CLIENT_CUSTOMER);
    }

    /**
     * @return \Pyz\Yves\Customer\Form\LoginForm
     */
    protected function createLoginForm()
    {
        return new LoginForm();
    }

    /**
     * @return \Pyz\Yves\Customer\Form\RegisterForm
     */
    protected function createRegisterForm()
    {
        return new RegisterForm();
    }

    /**
     * @return \Pyz\Yves\Customer\Form\GuestForm
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createGuestForm()
    {
        return new GuestForm($this->getUtilValidateService());
    }

    /**
     * @return \Spryker\Service\UtilValidate\UtilValidateServiceInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getUtilValidateService()
    {
        return $this->getProvidedDependency(CheckoutDependencyProvider::SERVICE_UTIL_VALIDATE);
    }

    /**
     * @return \Spryker\Client\Cart\CartClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCartClient()
    {
        return $this->getProvidedDependency(CheckoutDependencyProvider::CLIENT_CART);
    }

    /**
     * @return \Pyz\Client\Merchant\MerchantClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantClient(): MerchantClientInterface
    {
        return $this->getProvidedDependency(CheckoutDependencyProvider::CLIENT_MERCHANT);
    }

    /**
     * @return BranchFormDataProvider
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createBranchFormDataProvider()
    {
        return new BranchFormDataProvider(
            $this->getMerchantClient()
        );
    }

    /**
     * @return array
     */
    public function createBranchFormTypes()
    {
        return [
            new BranchForm(),
        ];
    }

    /**
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createBranchFormCollection()
    {
        return $this->createFormCollection($this->createBranchFormTypes(), $this->createBranchFormDataProvider());
    }


    /**
     * @return ConcreteTimeSlotFormDataProvider
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createConcreteTimeSlotFormDataProvider()
    {
        return new ConcreteTimeSlotFormDataProvider(
            $this->getMerchantClient()
        );
    }

    /**
     * @return array
     */
    public function createConcreteTimeSlotFormTypes()
    {
        return [
            new ConcreteTimeSlotForm(),
        ];
    }

    /**
     * @return \Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createConcreteTimeSlotFormCollection()
    {
        return $this->createFormCollection($this->createConcreteTimeSlotFormTypes(), $this->createConcreteTimeSlotFormDataProvider());
    }
}
