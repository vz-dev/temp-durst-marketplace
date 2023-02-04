<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Oms;

use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\ContinueTour;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\IntegraCancellation;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\MarkCancel;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\MarkCancelDeliveryStatus;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\RecalculateCancel;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\RefundAuthorization;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\RefundPayment;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\RevertTour;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\SaveCancellation;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\SendCancelMail;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Command\StartCancel;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Condition\IsIssuerDriver;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Condition\IsOrderCancelable;
use Pyz\Zed\CancelOrder\Communication\Plugin\OMS\Condition\IsTourExported;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\Authorize;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\CancelAuthorization;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\Capture;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\CaptureCancel;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\CaptureCharge;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\CompleteAuthorization;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\CreateInvoice as HeidelpayCreateInvoice;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\CreateShipment;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\MarkGraphmastersOrderCancelled as HeidelpayRestMarkGraphmastersOrderCancelled;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\Refund;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\SendFailMail;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\SendInvalidEmail;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\ShipmentCancel;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Command\ShipmentFinalize;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsAuthorizationCompleted;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCaptureApproved;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCaptureCancelApproved;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCaptureCancelFailLimitSucceeded;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCaptureCancelPendingOrCoreTimeout;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCaptureChargeApproved;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCaptureChargeFailLimitSucceeded;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCaptureChargePendingOrCoreTimeout;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCaptureFailLimitSucceeded;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCaptureNotApprovedAndBranchUsesGraphmasters;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCapturePendingOrCoreTimeout;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCustomerNotValidAndBranchUsesGraphmasters;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsCustomerValid;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsHeidelpayShipmentFailLimitSucceeded;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsHeidelpayShipmentPendingOrCoreTimeout;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsInvoiceCreated;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsRefundCompleted;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsShipmentCancelCompleted;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsShipmentCancelFailLimitSucceeded;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsShipmentCancelPendingOrCoreTimeout;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsShipmentCompleted;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsShipmentFinalizeCompleted;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsShipmentFinalizeFailLimitSucceeded;
use Pyz\Zed\HeidelpayRest\Communication\Plugin\OMS\Condition\IsShipmentFinalizePendingOrCoreTimeout;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\RetailOrder\AcceptOrderCommand;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\RetailOrder\ConfirmOrderCommand;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\RetailOrder\DeclineOrderCommand;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\RetailOrder\RateOrderCommand;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\AuthorizePayment;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\Board;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\ConfirmOrderCommand as WholesaleConfirmOrderCommand;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\ConfirmOrderSepa;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\CreateInvoice;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\EmptyCommand;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\FailConfirmation;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\HandlePayment;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\MarkDamage;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\MarkDecline;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\MarkDeliver;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\MarkGraphmastersOrderCancelled as WholesaleOrderMarkGraphmastersOrderCancelled;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\MarkLose;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\OrderDepositWholesaler;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\OrderWholesale;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\PlanTour;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\PreAuthorizePayment;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\Rate;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\Recalculate;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\SendInvoice;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\SendInvoiceInvoice;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\SendInvoiceSepa;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Command\WholesaleOrder\Ship;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\IsBoardingInvalid;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\IsDamaged;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\IsDeclined;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\IsDelivered;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\IsMissing;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\IsOrderConfirmed;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\IsOrderNotConfirmedAndBranchUsesGraphmasters;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\IsPaymentPreAuthorized;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\IsRecalculationInvalid;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\IsTourPlanned;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\IsWholesaleOrdered;
use Pyz\Zed\Oms\Communication\Plugin\Oms\Condition\WholesaleOrder\NeedsRefund;
use Pyz\Zed\Oms\Dependency\Facade\OmsToInvoiceBridge;
use Spryker\Zed\Availability\Communication\Plugin\AvailabilityHandlerPlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandCollectionInterface;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionCollectionInterface;
use Spryker\Zed\Oms\Dependency\Plugin\ReservationHandlerPluginInterface;
use Spryker\Zed\Oms\OmsDependencyProvider as SprykerOmsDependencyProvider;

class OmsDependencyProvider extends SprykerOmsDependencyProvider
{
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';
    public const FACADE_CUSTOMER = 'FACADE_CUSTOMER';
    public const FACADE_MAIL = 'FACADE_MAIL';
    public const FACADE_SALES = 'FACADE_SALES';
    public const FACADE_TOUR = 'FACADE_TOUR';
    public const FACADE_SEQUENCE_NUMBER = 'FACADE_SEQUENCE_NUMBER';
    public const FACADE_DEPOSIT = 'FACADE_DEPOSIT';
    public const FACADE_CALCULATION = 'FACADE_CALCULATION';
    public const FACADE_REFUND = 'FACADE_REFUND';
    public const FACADE_DRIVER = 'FACADE_DRIVER';
    public const FACADE_TAX = 'FACADE_TAX';
    public const FACADE_PRODUCT = 'FACADE_PRODUCT';
    public const FACADE_STATE_MACHINE = 'FACADE_STATE_MACHINE';
    public const FACADE_HEIDELPAY_REST = 'FACADE_HEIDELPAY_REST';
    public const FACADE_INVOICE = 'FACADE_INVOICE';
    public const FACADE_TERMS_OF_SERVICE = 'FACADE_TERMS_OF_SERVICE';
    public const FACADE_TOUCH = 'FACADE_TOUCH';
    public const FACADE_INTEGRA = 'FACADE_INTEGRA';
    public const FACADE_BILLING = 'FACADE_BILLING';
    public const FACADE_DISCOUNT = 'FACADE_DISCOUNT';
    public const FACADE_CANCEL_ORDER = 'FACADE_CANCEL_ORDER';
    public const FACADE_GRAPHMASTERS = 'FACADE_GRAPHMASTERS';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $this->addRetailOrderCommands($container);

        $this->addWholesaleOrderConditions($container);
        $this->addWholesaleOrderCommands($container);

        $this->addHeidelpayRestCommands($container);
        $this->addHeidelpayRestConditions($container);

        $this->addCancelOrderCommands($container);
        $this->addCancelOrderConditions($container);

        $container = $this->addSequenceNumberFacade($container);
        $container = $this->addSalesFacade($container);
        $container = $this->addRefundFacade($container);
        $container = $this->addTaxFacade($container);
        $container = $this->addCalculationFacade($container);
        $container = $this->addProductFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addHeidelpayRestFacade($container);
        $container = $this->addInvoiceFacade($container);
        $container = $this->addTermsOfServiceFacade($container);
        $container = $this->addDiscountFacade($container);

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addMailFacade($container);
        $container = $this->addCustomerFacade($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addSalesFacade($container);
        $container = $this->addRefundFacade($container);
        $container = $this->addTourFacade($container);
        $container = $this->addDriverFacade($container);
        $container = $this->addDepositFacade($container);
        $container = $this->addStateMachineFacade($container);
        $container = $this->addHeidelpayRestFacade($container);
        $container = $this->addInvoiceFacade($container);
        $container = $this->addTouchFacade($container);
        $container = $this->addIntegraFacade($container);
        $container = $this->addBillingFacade($container);
        $container = $this->addCancelOrderFacade($container);
        $container = $this->addGraphmastersFacade($container);

        return $container;
    }

    /**
     * @param Container $container
     */
    protected function addWholesaleOrderCommands(Container $container)
    {
        $container->extend(self::COMMAND_PLUGINS, function (CommandCollectionInterface $commandCollection) {
            $commandCollection->add(new AuthorizePayment(), 'WholesaleOrder/AuthorizePayment');
            $commandCollection->add(new Board(), 'WholesaleOrder/Board');
            $commandCollection->add(new CreateInvoice(), 'WholesaleOrder/CreateInvoice');
            $commandCollection->add(new HandlePayment(), 'WholesaleOrder/HandlePayment');
            $commandCollection->add(new OrderWholesale(), 'WholesaleOrder/OrderWholesale');
            $commandCollection->add(new PlanTour(), 'WholesaleOrder/PlanTour');
            $commandCollection->add(new PreAuthorizePayment(), 'WholesaleOrder/PreAuthorizePayment');
            $commandCollection->add(new Rate(), 'WholesaleOrder/Rate');
            $commandCollection->add(new Recalculate(), 'WholesaleOrder/Recalculate');
            $commandCollection->add(new SendInvoice(), 'WholesaleOrder/SendInvoice');
            $commandCollection->add(new SendInvoiceSepa(), 'WholesaleOrder/SendInvoiceSepa');
            $commandCollection->add(new SendInvoiceInvoice(), SendInvoiceInvoice::NAME);
            $commandCollection->add(new Ship(), 'WholesaleOrder/Ship');
            $commandCollection->add(new WholesaleConfirmOrderCommand(), 'WholesaleOrder/ConfirmOrder');
            $commandCollection->add(new ConfirmOrderSepa(), 'WholesaleOrder/ConfirmOrderSepa');
            $commandCollection->add(new OrderDepositWholesaler(), OrderDepositWholesaler::NAME);
            $commandCollection->add(new MarkDecline(), MarkDecline::NAME);
            $commandCollection->add(new MarkDeliver(), MarkDeliver::NAME);
            $commandCollection->add(new MarkLose(), MarkLose::NAME);
            $commandCollection->add(new MarkDamage(), MarkDamage::NAME);
            $commandCollection->add(new EmptyCommand(), EmptyCommand::NAME);
            $commandCollection->add(new FailConfirmation(), FailConfirmation::NAME);
            $commandCollection->add(new WholesaleOrderMarkGraphmastersOrderCancelled(), WholesaleOrderMarkGraphmastersOrderCancelled::NAME);

            return $commandCollection;
        });
    }

    /**
     * @param Container $container
     */
    protected function addWholesaleOrderConditions(Container $container)
    {
        $container->extend(self::CONDITION_PLUGINS, function (ConditionCollectionInterface $commandCollection) {
            $commandCollection->add(new IsPaymentPreAuthorized(), 'WholesaleOrder/IsPaymentPreAuthorized');
            $commandCollection->add(new IsTourPlanned(), 'WholesaleOrder/IsTourPlanned');
            $commandCollection->add(new IsWholesaleOrdered(), 'WholesaleOrder/IsWholesaleOrdered');
            $commandCollection->add(new IsBoardingInvalid(), 'WholesaleOrder/IsBoardingInvalid');
            $commandCollection->add(new IsRecalculationInvalid(), 'WholesaleOrder/IsRecalculationInvalid');
            $commandCollection->add(new NeedsRefund(), NeedsRefund::NAME);
            $commandCollection->add(new IsDamaged(), IsDamaged::NAME);
            $commandCollection->add(new IsMissing(), IsMissing::NAME);
            $commandCollection->add(new IsDeclined(), IsDeclined::NAME);
            $commandCollection->add(new IsDelivered(), IsDelivered::NAME);
            $commandCollection->add(new IsOrderConfirmed(), IsOrderConfirmed::NAME);
            $commandCollection->add(new IsOrderNotConfirmedAndBranchUsesGraphmasters(), IsOrderNotConfirmedAndBranchUsesGraphmasters::NAME);
            $commandCollection->add(new IsCaptureNotApprovedAndBranchUsesGraphmasters(), IsCaptureNotApprovedAndBranchUsesGraphmasters::NAME);

            return $commandCollection;
        });
    }

    /**
     * @param Container $container
     */
    protected function addRetailOrderCommands(Container $container)
    {
        $container->extend(self::COMMAND_PLUGINS, function (CommandCollectionInterface $commandCollection) {
            $commandCollection->add(new AcceptOrderCommand(), 'RetailOrder/AcceptOrder');
            $commandCollection->add(new DeclineOrderCommand(), 'RetailOrder/DeclineOrder');
            $commandCollection->add(new ConfirmOrderCommand(), 'RetailOrder/ConfirmOrder');
            $commandCollection->add(new RateOrderCommand(), 'RetailOrder/RateOrder');

            return $commandCollection;
        });
    }

    /**
     * @param Container $container
     */
    protected function addHeidelpayRestCommands(Container $container): void
    {
        $container->extend(self::COMMAND_PLUGINS, function (CommandCollectionInterface $commandCollection) {
            $commandCollection->add(new Capture(), Capture::NAME);
            $commandCollection->add(new Authorize(), Authorize::NAME);
            $commandCollection->add(new CancelAuthorization(), CancelAuthorization::NAME);
            $commandCollection->add(new CompleteAuthorization(), CompleteAuthorization::NAME);
            $commandCollection->add(new Refund(), Refund::NAME);
            $commandCollection->add(new HeidelpayCreateInvoice(), HeidelpayCreateInvoice::NAME);
            $commandCollection->add(new CreateShipment(), CreateShipment::NAME);
            $commandCollection->add(new SendInvalidEmail(), SendInvalidEmail::NAME);
            $commandCollection->add(new SendFailMail(), SendFailMail::NAME);
            $commandCollection->add(new CaptureCharge(), CaptureCharge::NAME);
            $commandCollection->add(new CaptureCancel(), CaptureCancel::NAME);
            $commandCollection->add(new ShipmentCancel(), ShipmentCancel::NAME);
            $commandCollection->add(new ShipmentFinalize(), ShipmentFinalize::NAME);
            $commandCollection->add(new HeidelpayRestMarkGraphmastersOrderCancelled(), HeidelpayRestMarkGraphmastersOrderCancelled::NAME);

            return $commandCollection;
        });
    }

    /**
     * @param Container $container
     */
    protected function addHeidelpayRestConditions(Container $container): void
    {
        $container->extend(self::CONDITION_PLUGINS, function (ConditionCollectionInterface $conditionCollection) {
            $conditionCollection->add(new IsAuthorizationCompleted(), IsAuthorizationCompleted::NAME);
            $conditionCollection->add(new IsCaptureApproved(), IsCaptureApproved::NAME);
            $conditionCollection->add(new IsRefundCompleted(), IsRefundCompleted::NAME);
            $conditionCollection->add(new IsInvoiceCreated(), IsInvoiceCreated::NAME);
            $conditionCollection->add(new IsShipmentCompleted(), IsShipmentCompleted::NAME);
            $conditionCollection->add(new IsCustomerValid(), IsCustomerValid::NAME);
            $conditionCollection->add(new IsCapturePendingOrCoreTimeout(), IsCapturePendingOrCoreTimeout::NAME);
            $conditionCollection->add(new IsCaptureFailLimitSucceeded(), IsCaptureFailLimitSucceeded::NAME);
            $conditionCollection->add(new IsHeidelpayShipmentPendingOrCoreTimeout(), IsHeidelpayShipmentPendingOrCoreTimeout::NAME);
            $conditionCollection->add(new IsHeidelpayShipmentFailLimitSucceeded(), IsHeidelpayShipmentFailLimitSucceeded::NAME);
            $conditionCollection->add(new IsCaptureChargeApproved(), IsCaptureChargeApproved::NAME);
            $conditionCollection->add(new IsCaptureChargePendingOrCoreTimeout(), IsCaptureChargePendingOrCoreTimeout::NAME);
            $conditionCollection->add(new IsCaptureChargeFailLimitSucceeded(), IsCaptureChargeFailLimitSucceeded::NAME);
            $conditionCollection->add(new IsCaptureCancelApproved(), IsCaptureCancelApproved::NAME);
            $conditionCollection->add(new IsCaptureCancelPendingOrCoreTimeout(), IsCaptureCancelPendingOrCoreTimeout::NAME);
            $conditionCollection->add(new IsCaptureCancelFailLimitSucceeded(), IsCaptureCancelFailLimitSucceeded::NAME);
            $conditionCollection->add(new IsShipmentCancelCompleted(), IsShipmentCancelCompleted::NAME);
            $conditionCollection->add(new IsShipmentCancelPendingOrCoreTimeout(), IsShipmentCancelPendingOrCoreTimeout::NAME);
            $conditionCollection->add(new IsShipmentCancelFailLimitSucceeded(), IsShipmentCancelFailLimitSucceeded::NAME);
            $conditionCollection->add(new IsShipmentFinalizeCompleted(), IsShipmentFinalizeCompleted::NAME);
            $conditionCollection->add(new IsShipmentFinalizePendingOrCoreTimeout(), IsShipmentFinalizePendingOrCoreTimeout::NAME);
            $conditionCollection->add(new IsShipmentFinalizeFailLimitSucceeded(), IsShipmentFinalizeFailLimitSucceeded::NAME);
            $conditionCollection->add(new IsCustomerNotValidAndBranchUsesGraphmasters(), IsCustomerNotValidAndBranchUsesGraphmasters::NAME);
            $conditionCollection->add(new IsCaptureNotApprovedAndBranchUsesGraphmasters(), IsCaptureNotApprovedAndBranchUsesGraphmasters::NAME);

            return $conditionCollection;
        });
    }

    /**
     * @param Container $container
     * @return void
     */
    protected function addCancelOrderCommands(Container $container): void
    {
        $container->extend(self::COMMAND_PLUGINS, function (CommandCollectionInterface $commandCollection) {
            $commandCollection->add(new StartCancel(), StartCancel::NAME);
            $commandCollection->add(new RefundAuthorization(), RefundAuthorization::NAME);
            $commandCollection->add(new RefundPayment(), RefundPayment::NAME);
            $commandCollection->add(new RecalculateCancel(), RecalculateCancel::NAME);
            $commandCollection->add(new RevertTour(), RevertTour::NAME);
            $commandCollection->add(new SendCancelMail(), SendCancelMail::NAME);
            $commandCollection->add(new IntegraCancellation(), IntegraCancellation::NAME);
            $commandCollection->add(new SaveCancellation(), SaveCancellation::NAME);
            $commandCollection->add(new ContinueTour(), ContinueTour::NAME);
            $commandCollection->add(new MarkCancel(), MarkCancel::NAME);
            $commandCollection->add(new MarkCancelDeliveryStatus(), MarkCancelDeliveryStatus::NAME);

            return $commandCollection;
        });
    }

    /**
     * @param Container $container
     * @return void
     */
    protected function addCancelOrderConditions(Container $container): void
    {
        $container->extend(self::CONDITION_PLUGINS, function (ConditionCollectionInterface $conditionCollection) {
            $conditionCollection->add(new IsOrderCancelable(), IsOrderCancelable::NAME);
            $conditionCollection->add(new IsIssuerDriver(), IsIssuerDriver::NAME);
            $conditionCollection->add(new IsTourExported(), IsTourExported::NAME);

            return $conditionCollection;
        });
    }

    /**
     * @param Container $container
     *
     * @return ReservationHandlerPluginInterface[]
     */
    protected function getReservationHandlerPlugins(Container $container): array
    {
        return [
            new AvailabilityHandlerPlugin(),
        ];
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addMailFacade(Container $container): Container
    {
        $container[self::FACADE_MAIL] = function (Container $container) {
            return $container->getLocator()->mail()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addCustomerFacade(Container $container): Container
    {
        $container[self::FACADE_CUSTOMER] = function (Container $container) {
            return $container->getLocator()->customer()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addMerchantFacade(Container $container): Container
    {
        $container[static::FACADE_MERCHANT] = function (Container $container) {
            return $container->getLocator()->merchant()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addSalesFacade(Container $container): Container
    {
        $container[static::FACADE_SALES] = function (Container $container) {
            return $container->getLocator()->sales()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addTourFacade(Container $container): Container
    {
        $container[static::FACADE_TOUR] = function (Container $container) {
            return $container->getLocator()->tour()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addSequenceNumberFacade(Container $container): Container
    {
        $container[static::FACADE_SEQUENCE_NUMBER] = function (Container $container) {
            return $container->getLocator()->sequenceNumber()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addDepositFacade(Container $container): Container
    {
        $container[static::FACADE_DEPOSIT] = function (Container $container) {
            return $container->getLocator()->deposit()->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addCalculationFacade(Container $container): Container
    {
        $container[self::FACADE_CALCULATION] = function (Container $container) {
            return $container
                ->getLocator()
                ->calculation()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addRefundFacade(Container $container): Container
    {
        $container[self::FACADE_REFUND] = function (Container $container) {
            return $container
                ->getLocator()
                ->refund()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addDriverFacade(Container $container): Container
    {
        $container[self::FACADE_DRIVER] = function (Container $container) {
            return $container
                ->getLocator()
                ->driver()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addTaxFacade(Container $container): Container
    {
        $container[self::FACADE_TAX] = function (Container $container) {
            return $container
                ->getLocator()
                ->tax()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addProductFacade(Container $container): Container
    {
        $container[self::FACADE_PRODUCT] = function (Container $container) {
            return $container
                ->getLocator()
                ->product()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addStateMachineFacade(Container $container): Container
    {
        $container[self::FACADE_STATE_MACHINE] = function (Container $container) {
            return $container
                ->getLocator()
                ->stateMachine()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addHeidelpayRestFacade(Container $container): Container
    {
        $container[self::FACADE_HEIDELPAY_REST] = function (Container $container) {
            return $container
                ->getLocator()
                ->heidelpayRest()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addInvoiceFacade(Container $container): Container
    {
        $container[static::FACADE_INVOICE] = function (Container $container) {
            return new OmsToInvoiceBridge(
                $container
                    ->getLocator()
                    ->invoice()
                    ->facade()
            );
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addTermsOfServiceFacade(Container $container): Container
    {
        $container[self::FACADE_TERMS_OF_SERVICE] = function (Container $container) {
            return $container
                ->getLocator()
                ->termsOfService()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addTouchFacade(Container $container): Container
    {
        $container[static::FACADE_TOUCH] = function (Container $container) {
            return $container
                ->getLocator()
                ->touch()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addIntegraFacade(Container $container): Container
    {
        $container[static::FACADE_INTEGRA] = function (Container $container) {
            return $container
                ->getLocator()
                ->integra()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addBillingFacade(Container $container): Container
    {
        $container[static::FACADE_BILLING] = function (Container $container) {
            return $container
                ->getLocator()
                ->billing()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addDiscountFacade(Container $container): Container
    {
        $container[static::FACADE_DISCOUNT] = function (Container $container) {
            return $container
                ->getLocator()
                ->discount()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addCancelOrderFacade(Container $container): Container
    {
        $container[static::FACADE_CANCEL_ORDER] = function (Container $container) {
            return $container
                ->getLocator()
                ->cancelOrder()
                ->facade();
        };

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addGraphmastersFacade(Container $container): Container
    {
        $container[static::FACADE_GRAPHMASTERS] = function (Container $container) {
            return $container
                ->getLocator()
                ->graphMasters()
                ->facade();
        };

        return $container;
    }
}
