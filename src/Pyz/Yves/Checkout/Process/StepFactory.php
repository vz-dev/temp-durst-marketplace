<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Yves\Checkout\Process;

use Pyz\Yves\Application\Plugin\Provider\ApplicationControllerProvider;
use Pyz\Yves\Checkout\CheckoutDependencyProvider;
use Pyz\Yves\Checkout\Plugin\Provider\CheckoutControllerProvider;
use Pyz\Yves\Checkout\Process\Steps\AddressStep;
use Pyz\Yves\Checkout\Process\Steps\ConcreteTimeSlotStep;
use Pyz\Yves\Checkout\Process\Steps\CustomerStep;
use Pyz\Yves\Checkout\Process\Steps\EntryStep;
use Pyz\Yves\Checkout\Process\Steps\PaymentStep;
use Pyz\Yves\Checkout\Process\Steps\PlaceOrderStep;
use Pyz\Yves\Checkout\Process\Steps\ShipmentStep;
use Pyz\Yves\Checkout\Process\Steps\SuccessStep;
use Pyz\Yves\Checkout\Process\Steps\SummaryStep;
use Pyz\Yves\Customer\Plugin\Provider\CustomerControllerProvider;
use Spryker\Yves\Checkout\Process\StepFactory as SprykerStepFactory;
use Spryker\Yves\ProductBundle\Grouper\ProductBundleGrouper;
use Spryker\Yves\StepEngine\Process\StepBreadcrumbGenerator;
use Spryker\Yves\StepEngine\Process\StepCollection;
use Spryker\Yves\StepEngine\Process\StepCollectionInterface;
use Spryker\Yves\StepEngine\Process\StepEngine;
use Pyz\Yves\Checkout\Process\Steps\BranchSelectionStep;

class StepFactory extends SprykerStepFactory
{
    /**
     * @param \Spryker\Yves\StepEngine\Process\StepCollectionInterface $stepCollection
     *
     * @return \Spryker\Yves\StepEngine\Process\StepEngine
     */
    public function createStepEngine(StepCollectionInterface $stepCollection)
    {
        return new StepEngine($stepCollection, $this->createDataContainer(), $this->createStepBreadcrumbGenerator());
    }

    /**
     * @return \Spryker\Yves\StepEngine\Process\StepCollectionInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createStepCollection()
    {
        $stepCollection = new StepCollection(
            $this->getUrlGenerator(),
            CheckoutControllerProvider::CHECKOUT_ERROR
        );

        $stepCollection
            ->addStep($this->createEntryStep())
            ->addStep($this->createCustomerStep())
            ->addStep($this->createAddressStep())
            ->addStep($this->createShipmentStep())
            ->addStep($this->createPaymentStep())
            ->addStep($this->createBranchSelectionStep())
            ->addStep($this->createConcreteTimeSlotStep())
            ->addStep($this->createSummaryStep())
            ->addStep($this->createPlaceOrderStep())
            ->addStep($this->createSuccessStep());

        return $stepCollection;
    }

    /**
     * @return \Pyz\Yves\Checkout\Process\Steps\EntryStep
     */
    protected function createEntryStep()
    {
        return new EntryStep(
            CheckoutControllerProvider::CHECKOUT_INDEX,
            ApplicationControllerProvider::ROUTE_HOME
        );
    }

    /**
     * @return \Pyz\Yves\Checkout\Process\Steps\CustomerStep
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createCustomerStep()
    {
        return new CustomerStep(
            $this->getProvidedDependency(CheckoutDependencyProvider::CLIENT_CUSTOMER),
            $this->getCustomerStepHandler(),
            CheckoutControllerProvider::CHECKOUT_CUSTOMER,
            ApplicationControllerProvider::ROUTE_HOME,
            $this->getApplication()->path(CustomerControllerProvider::ROUTE_LOGOUT)
        );
    }

    /**
     * @return \Pyz\Yves\Checkout\Process\Steps\AddressStep
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createAddressStep()
    {
        return new AddressStep(
            $this->getProvidedDependency(CheckoutDependencyProvider::CLIENT_CUSTOMER),
            $this->getProvidedDependency(CheckoutDependencyProvider::CLIENT_CALCULATION),
            CheckoutControllerProvider::CHECKOUT_ADDRESS,
            ApplicationControllerProvider::ROUTE_HOME
        );
    }

    /**
     * @return \Pyz\Yves\Checkout\Process\Steps\ShipmentStep
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createShipmentStep()
    {
        return new ShipmentStep(
            $this->getCalculationClient(),
            $this->getShipmentPlugins(),
            CheckoutControllerProvider::CHECKOUT_SHIPMENT,
            ApplicationControllerProvider::ROUTE_HOME
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
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginCollection
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getShipmentPlugins()
    {
        return $this->getProvidedDependency(CheckoutDependencyProvider::PLUGIN_SHIPMENT_HANDLER);
    }

    /**
     * @return PaymentStep
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createPaymentStep()
    {
        return new PaymentStep(
            $this->createPaymentMethodHandler(),
            CheckoutControllerProvider::CHECKOUT_PAYMENT,
            ApplicationControllerProvider::ROUTE_HOME,
            $this->getFlashMessenger()
        );
    }

    /**
     * @return \Pyz\Yves\Checkout\Process\Steps\SummaryStep
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createSummaryStep()
    {
        return new SummaryStep(
            $this->createProductBundleGrouper(),
            $this->getCartClient(),
            CheckoutControllerProvider::CHECKOUT_SUMMARY,
            ApplicationControllerProvider::ROUTE_HOME
        );
    }

    /**
     * @return \Pyz\Yves\Checkout\Process\Steps\PlaceOrderStep
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createPlaceOrderStep()
    {
        return new PlaceOrderStep(
            $this->getCheckoutClient(),
            $this->getFlashMessenger(),
            CheckoutControllerProvider::CHECKOUT_PLACE_ORDER,
            ApplicationControllerProvider::ROUTE_HOME,
            [
                'payment failed' => CheckoutControllerProvider::CHECKOUT_PAYMENT,
            ]
        );
    }

    /**
     * @return \Pyz\Yves\Checkout\Process\Steps\SuccessStep
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function createSuccessStep()
    {
        return new SuccessStep(
            $this->getProvidedDependency(CheckoutDependencyProvider::CLIENT_CUSTOMER),
            $this->getCartClient(),
            CheckoutControllerProvider::CHECKOUT_SUCCESS,
            ApplicationControllerProvider::ROUTE_HOME
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCustomerStepHandler()
    {
        return $this->getProvidedDependency(CheckoutDependencyProvider::PLUGIN_CUSTOMER_STEP_HANDLER);
    }

    /**
     * @return \Spryker\Yves\Messenger\FlashMessenger\FlashMessengerInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getFlashMessenger()
    {
        return $this->getApplication()['flash_messenger'];
    }

    /**
     * @return \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getUrlGenerator()
    {
        return $this->getApplication()['url_generator'];
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
     * @return \Spryker\Client\Calculation\CalculationClient
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCalculationClient()
    {
        return $this->getProvidedDependency(CheckoutDependencyProvider::CLIENT_CALCULATION);
    }

    /**
     * @return \Spryker\Client\Checkout\CheckoutClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCheckoutClient()
    {
        return $this->getProvidedDependency(CheckoutDependencyProvider::CLIENT_CHECKOUT);
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
     * @return \Spryker\Yves\StepEngine\Process\StepBreadcrumbGeneratorInterface
     */
    public function createStepBreadcrumbGenerator()
    {
        return new StepBreadcrumbGenerator();
    }

    /**
     * @return BranchSelectionStep
     */
    protected function createBranchSelectionStep()
    {
        return new BranchSelectionStep(
            CheckoutControllerProvider::CHECKOUT_BRANCH,
            ApplicationControllerProvider::ROUTE_HOME
        );
    }

    /**
     * @return ConcreteTimeSlotStep
     */
    protected function createConcreteTimeSlotStep()
    {
        return new ConcreteTimeSlotStep(
            CheckoutControllerProvider::CHECKOUT_CONCRETETIMESLOT,
            ApplicationControllerProvider::ROUTE_HOME
        );
    }
}
