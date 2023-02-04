<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 24.01.18
 * Time: 10:53
 */

namespace Pyz\Zed\Oms\Business;

use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Oms\Business\Model\Durst\DurstCompanyDetailsManager;
use Pyz\Zed\Oms\Business\Model\Durst\DurstCompanyDetailsManagerInterface;
use Pyz\Zed\Oms\Business\Model\InvoiceManager;
use Pyz\Zed\Oms\Business\Model\InvoiceManagerInterface;
use Pyz\Zed\Oms\Business\Model\InvoiceReferenceGenerator;
use Pyz\Zed\Oms\Business\Model\InvoiceReferenceGeneratorInterface;
use Pyz\Zed\Oms\Business\Model\Mail\BillingMailManager;
use Pyz\Zed\Oms\Business\Model\Mail\BillingMailManagerInterface;
use Pyz\Zed\Oms\Business\Model\Mail\InvoiceMailManager;
use Pyz\Zed\Oms\Business\Model\Mail\InvoiceMailManagerInterface;
use Pyz\Zed\Oms\Business\Model\Mail\RefundMailManager;
use Pyz\Zed\Oms\Business\Model\Mail\RefundMailManagerInterface;
use Pyz\Zed\Oms\Business\Model\Order\DiscountManager;
use Pyz\Zed\Oms\Business\Model\Order\DiscountManagerInterface;
use Pyz\Zed\Oms\Business\Model\Order\DriverManager;
use Pyz\Zed\Oms\Business\Model\Order\DriverManagerInterface;
use Pyz\Zed\Oms\Business\Model\Order\ExpenseManager;
use Pyz\Zed\Oms\Business\Model\Order\RefundExpandedItemsManager;
use Pyz\Zed\Oms\Business\Model\Order\RefundManager;
use Pyz\Zed\Oms\Business\Model\Order\SignatureManager;
use Pyz\Zed\Oms\Business\Model\Order\SignatureManagerInterface;
use Pyz\Zed\Oms\Business\Model\TransitionLog;
use Pyz\Zed\Oms\Business\Model\TransitionLogInterface;
use Pyz\Zed\Oms\Business\OrderStateMachine\StuckOrderDetector;
use Pyz\Zed\Oms\Business\OrderStateMachine\StuckOrderDetectorInterface;
use Pyz\Zed\Oms\Business\Util\OrderItemMatrix;
use Pyz\Zed\Oms\Dependency\Facade\OmsToInvoiceBridgeInterface;
use Pyz\Zed\Oms\OmsConfig;
use Pyz\Zed\Oms\OmsDependencyProvider;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Product\Business\ProductFacadeInterface;
use Pyz\Zed\Refund\Business\RefundFacadeInterface;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;
use Pyz\Zed\TermsOfService\Business\TermsOfServiceFacadeInterface;
use Spryker\Zed\Calculation\Business\CalculationFacadeInterface;
use Spryker\Zed\Oms\Business\OmsBusinessFactory as SprykerOmsBusinessFactory;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

/**
 * Class OmsBusinessFactory
 * @package Pyz\Zed\Oms\Business
 * @method OmsConfig getConfig()
 * @method OmsQueryContainerInterface getQueryContainer()
 */
class OmsBusinessFactory extends SprykerOmsBusinessFactory
{
    /**
     * @return \Pyz\Zed\Oms\Business\Model\InvoiceManagerInterface
     */
    public function createInvoiceManager(): InvoiceManagerInterface
    {
        return new InvoiceManager(
            $this->getSalesFacade(),
            $this->getInvoiceFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Oms\Business\Model\Mail\InvoiceMailManagerInterface
     */
    public function createInvoiceMailManager(): InvoiceMailManagerInterface
    {
        return new InvoiceMailManager(
            $this->getMailFacade(),
            $this->getConfig(),
            $this->createDurstCompanyDetailsManager(),
            $this->getHeidelpayRestFacade(),
            $this->getInvoiceFacade(),
            $this->getTaxFacade(),
            $this->getTermsOfServiceFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Oms\Business\Model\Order\DiscountManagerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createDiscountManager(): DiscountManagerInterface
    {
        return new DiscountManager(
            $this->getSalesFacade(),
            $this->getDiscountFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Oms\Business\Model\InvoiceReferenceGeneratorInterface
     */
    protected function createInvoiceReferenceGenerator(): InvoiceReferenceGeneratorInterface
    {
        return new InvoiceReferenceGenerator(
            $this->getSequenceNumberFacade(),
            $this->getConfig()->getInvoiceReferenceDefaults()
        );
    }

    /**
     * @return \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return int
     */
    protected function getCurrentBranchId(): int
    {
        return $this->getMerchantFacade()->getCurrentBranch()->getIdBranch();
    }

    /**
     * @return \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected function getSalesFacade()
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_SALES);
    }

    /**
     * @return \Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface
     */
    protected function getSequenceNumberFacade(): SequenceNumberFacadeInterface
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_SEQUENCE_NUMBER);
    }

    /**
     * @return \Pyz\Zed\Oms\Business\Model\Durst\DurstCompanyDetailsManagerInterface
     */
    public function createDurstCompanyDetailsManager(): DurstCompanyDetailsManagerInterface
    {
        return new DurstCompanyDetailsManager(
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Oms\Business\Model\Mail\RefundMailManagerInterface
     */
    public function createRefundMailManager(): RefundMailManagerInterface
    {
        return new RefundMailManager(
            $this->getMailFacade(),
            $this->createDurstCompanyDetailsManager(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Oms\Business\Model\Order\ExpenseManager
     */
    public function createExpenseManager(): ExpenseManager
    {
        return new ExpenseManager(
            $this->getSalesFacade(),
            $this->getCalculationFacade(),
            $this->getRefundFacade(),
            $this->getTaxFacade(),
            $this->getMerchantFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Oms\Business\Model\Order\RefundManager
     */
    public function createRefundManager(): RefundManager
    {
        return new RefundManager(
            $this->getSalesFacade(),
            $this->getRefundFacade(),
            $this->getMerchantFacade(),
            $this->getProductFacade()
        );
    }

    /**
     * @return RefundExpandedItemsManager
     */
    public function createRefundExpandedItemsManager() : RefundExpandedItemsManager
    {
        return new RefundExpandedItemsManager(
            $this->getSalesFacade(),
            $this->getRefundFacade(),
            $this->getMerchantFacade(),
            $this->getProductFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Oms\Business\Model\Order\SignatureManagerInterface
     */
    public function createSignatureManager(): SignatureManagerInterface
    {
        return new SignatureManager(
            $this->getSalesFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Oms\Business\Model\Order\DriverManagerInterface
     */
    public function createDriverManager(): DriverManagerInterface
    {
        return new DriverManager(
            $this->getSalesFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Oms\Business\Model\TransitionLogInterface
     */
    public function createTransitionLogModel(): TransitionLogInterface
    {
        return new TransitionLog(
            $this->getQueryContainer(),
            $this->getConfig()
        );
    }

    /**
     * @return BillingMailManagerInterface
     */
    public function createBillingMailManager(): BillingMailManagerInterface
    {
        return new BillingMailManager(
            $this->getMailFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return StuckOrderDetectorInterface
     */
    public function createStuckOrderDetector(): StuckOrderDetectorInterface
    {
        return new StuckOrderDetector(
            $this->getSalesFacade(),
            $this->getMailFacade(),
            $this->getConfig(),
            $this->createOrderStateMachineFinder()
        );
    }

    /**
     * @return OrderItemMatrix
     */
    public function createUtilOrderItemMatrix(): OrderItemMatrix
    {
        return new OrderItemMatrix(
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->getUtilSanitizeService()
        );
    }

    /**
     * @return \Pyz\Zed\Refund\Business\RefundFacadeInterface
     */
    protected function getRefundFacade(): RefundFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_REFUND
            );
    }

    /**
     * @return \Spryker\Zed\Calculation\Business\CalculationFacadeInterface
     */
    protected function getCalculationFacade(): CalculationFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_CALCULATION
            );
    }

    /**
     * @return TaxFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getTaxFacade(): TaxFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_TAX
            );
    }

    /**
     * @return \Pyz\Zed\Product\Business\ProductFacadeInterface
     */
    protected function getProductFacade(): ProductFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_PRODUCT
            );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\HeidelpayRestFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getHeidelpayRestFacade(): HeidelpayRestFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_HEIDELPAY_REST
            );
    }

    /**
     * @return \Pyz\Zed\Oms\Dependency\Facade\OmsToInvoiceBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getInvoiceFacade(): OmsToInvoiceBridgeInterface
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_INVOICE);
    }

    /**
     * @return TermsOfServiceFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getTermsOfServiceFacade(): TermsOfServiceFacadeInterface
    {
        return $this
            ->getProvidedDependency(OmsDependencyProvider::FACADE_TERMS_OF_SERVICE);
    }

    /**
     * @return \Pyz\Zed\Discount\Business\DiscountFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDiscountFacade(): DiscountFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                OmsDependencyProvider::FACADE_DISCOUNT
            );
    }
}
