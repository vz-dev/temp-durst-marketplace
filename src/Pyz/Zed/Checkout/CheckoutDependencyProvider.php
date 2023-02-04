<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Checkout;

use Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout\ConcreteTimeSlotAssertionPreConditionPlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout\ConcreteTimeSlotTouchCheckoutPostSaveHookPlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout\DeliveryAddressZipCodePreConditionPlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Checkout\DeliveryCostOrderSaverPlugin;
use Pyz\Zed\Deposit\Communication\Plugin\Checkout\SalesExpenseDepositExpanderPlugin;
use Pyz\Zed\Deposit\Communication\Plugin\DepositOrderSaverPlugin;
use Pyz\Zed\Discount\Communication\Plugin\Checkout\GlobalVoucherOrderSaverPlugin;
use Pyz\Zed\GoogleApi\Communication\Plugin\GoogleApiAddressLatLngOrderSaverPlugin;
use Pyz\Zed\GraphMasters\Communication\Plugin\Checkout\GraphMastersImportOrderPostCheckoutPlugin;
use Pyz\Zed\GraphMasters\Communication\Plugin\Checkout\GraphMastersOrderSavePlugin;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\Checkout\HeidelpayRestPostSavePlugin;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\Checkout\HeidelpayRestSaveOrderPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\BranchIsActiveCheckoutPreConditionPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\Checkout\BranchSupportsPaymentMethodPreConditionPlugin;
use Pyz\Zed\Merchant\Communication\Plugin\Checkout\SumUpOrderedUnitsPlugin;
use Pyz\Zed\Sales\Communication\Plugin\Checkout\AddCommentPlugin;
use Pyz\Zed\Sales\Communication\Plugin\Checkout\AddDurstCustomerReferencePlugin;
use Pyz\Zed\Sales\Communication\Plugin\Checkout\AddIntegraCustomerPlugin;
use Pyz\Zed\Tour\Communication\Plugin\Checkout\TimeSlotHasTourPreConditionPlugin;
use Spryker\Zed\Availability\Communication\Plugin\ProductsAvailableCheckoutPreConditionPlugin;
use Spryker\Zed\Checkout\CheckoutDependencyProvider as SprykerCheckoutDependencyProvider;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPostSaveHookInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreConditionInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreSaveHookInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreSaveInterface;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutSaveOrderInterface;
use Spryker\Zed\Customer\Communication\Plugin\Checkout\CustomerOrderSavePlugin;
use Spryker\Zed\Customer\Communication\Plugin\CustomerPreConditionCheckerPlugin;
use Spryker\Zed\Discount\Communication\Plugin\Checkout\DiscountOrderSavePlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Payment\Communication\Plugin\Checkout\PaymentOrderSaverPlugin;
use Spryker\Zed\Payment\Communication\Plugin\Checkout\PaymentPostCheckPlugin;
use Spryker\Zed\Payment\Communication\Plugin\Checkout\PaymentPreCheckPlugin;
use Spryker\Zed\Sales\Communication\Plugin\Checkout\SalesOrderExpanderPlugin;
use Spryker\Zed\Sales\Communication\Plugin\Checkout\SalesOrderSaverPlugin;
use Spryker\Zed\SalesProductConnector\Communication\Plugin\Checkout\ItemMetadataSaverPlugin;

class CheckoutDependencyProvider extends SprykerCheckoutDependencyProvider
{
    /**
     * @param Container $container ’
     *
     * @return CheckoutPreConditionInterface[]
     */
    protected function getCheckoutPreConditions(Container $container)
    {
        return [
            new CustomerPreConditionCheckerPlugin(),
            new ProductsAvailableCheckoutPreConditionPlugin(),
            new DeliveryAddressZipCodePreConditionPlugin(),
            new BranchIsActiveCheckoutPreConditionPlugin(),
            new BranchSupportsPaymentMethodPreConditionPlugin(),
            new ConcreteTimeSlotAssertionPreConditionPlugin(),
            new PaymentPreCheckPlugin(),
            new TimeSlotHasTourPreConditionPlugin(),
        ];
    }

    /**
     * @param Container $container
     *
     * @return CheckoutSaveOrderInterface[]
     */
    protected function getCheckoutOrderSavers(Container $container)
    {
        return [
            new CustomerOrderSavePlugin(),
            new SalesOrderSaverPlugin(),
            new GraphMastersOrderSavePlugin(),
            new AddCommentPlugin,
            new ItemMetadataSaverPlugin(),
            new DepositOrderSaverPlugin(),
            new DeliveryCostOrderSaverPlugin(),
            new GlobalVoucherOrderSaverPlugin(),
            new DiscountOrderSavePlugin(),
            new PaymentOrderSaverPlugin(),
            new HeidelpayRestSaveOrderPlugin(),
            new SumUpOrderedUnitsPlugin(),
            new GoogleApiAddressLatLngOrderSaverPlugin(),
            new AddIntegraCustomerPlugin(),
            new AddDurstCustomerReferencePlugin(),
        ];
    }

    /**
     * @param Container $container
     *
     * @return CheckoutPostSaveHookInterface[]
     */
    protected function getCheckoutPostHooks(Container $container)
    {
        return [
            new PaymentPostCheckPlugin(),
            new ConcreteTimeSlotTouchCheckoutPostSaveHookPlugin(),
            new HeidelpayRestPostSavePlugin(),
            new GraphMastersImportOrderPostCheckoutPlugin(),
        ];
    }

    /**
     * @param Container $container
     *
     * @return CheckoutPreSaveHookInterface[]|CheckoutPreSaveInterface[]
     */
    protected function getCheckoutPreSaveHooks(Container $container)
    {
        return [
            new SalesOrderExpanderPlugin(),
            new SalesExpenseDepositExpanderPlugin(),
        ];
    }
}
