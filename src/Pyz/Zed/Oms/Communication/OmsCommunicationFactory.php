<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 12.02.18
 * Time: 14:50
 */

namespace Pyz\Zed\Oms\Communication;

use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Pyz\Zed\Driver\Business\DriverFacadeInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Oms\Dependency\Facade\OmsToInvoiceBridge;
use Pyz\Zed\Oms\OmsConfig;
use Pyz\Zed\Oms\OmsDependencyProvider;
use Pyz\Zed\Oms\Persistence\OmsQueryContainer;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Refund\Business\RefundFacadeInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Touch\Business\TouchFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Customer\Business\CustomerFacadeInterface;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Mail\Business\MailFacadeInterface;
use Spryker\Zed\Oms\Communication\OmsCommunicationFactory as SprykerOmsCommunicationFactory;
use Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface;

/**
 * @method OmsConfig getConfig()
 * @method OmsQueryContainer getQueryContainer()
 */
class OmsCommunicationFactory extends SprykerOmsCommunicationFactory
{
    /**
     * @return MailFacadeInterface
     */
    public function getMailFacade()
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_MAIL);
    }

    /**
     * @return \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     * @return DiscountFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDiscountFacade() : DiscountFacadeInterface
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_DISCOUNT);
    }

    /**
     * @return MerchantFacadeInterface
     */
    public function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return CustomerFacadeInterface
     */
    public function getCustomerFacade()
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_CUSTOMER);
    }

    /**
     * @return SalesFacadeInterface
     */
    public function getSalesFacade()
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_SALES);
    }

    /**
     * @return DriverFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDriverFacade(): DriverFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_DRIVER
            );
    }
    /**
     * @return TourFacadeInterface
     */
    public function getTourFacade(): TourFacadeInterface
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_TOUR);
    }

    /**
     * @return DepositFacadeInterface
     */
    public function getDepositFacade(): DepositFacadeInterface
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_DEPOSIT);
    }

    /**
     * @return RefundFacadeInterface
     */
    public function getRefundFacade(): RefundFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_REFUND
            );
    }

    /**
     * @return StateMachineFacadeInterface
     */
    public function getStateMachineFacade(): StateMachineFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_STATE_MACHINE
            );
    }

    /**
     * @return OmsQueryContainerInterface
     */
    public function getOmsQueryContainer(): OmsQueryContainerInterface
    {
        return $this
            ->getQueryContainer();
    }

    /**
     * @return HeidelpayRestFacadeInterface
     */
    public function getHeidelpayRestFacade(): HeidelpayRestFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_HEIDELPAY_REST
            );
    }

    /**
     * @return OmsToInvoiceBridge
     */
    public function getInvoiceFacade() : OmsToInvoiceBridge
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_INVOICE);
    }

    /**
     * @return TouchFacadeInterface
     */
    public function getTouchFacade(): TouchFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_TOUCH
            );
    }

    /**
     * @return IntegraFacadeInterface
     */
    public function getIntegraFacade(): IntegraFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_INTEGRA
            );
    }

    /**
     * @return BillingFacadeInterface
     */
    public function getBillingFacade(): BillingFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_BILLING
            );
    }

    /**
     * @return \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCancelOrderFacade(): CancelOrderFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_CANCEL_ORDER
            );
    }

    /**
     * @return GraphMastersFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getGraphMastersFacade(): GraphMastersFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_GRAPHMASTERS
            );
    }
}
