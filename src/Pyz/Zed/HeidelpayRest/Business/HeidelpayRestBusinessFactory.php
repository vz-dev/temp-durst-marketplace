<?php
/**
 * Durst - project - HeidelpayRestBusinessFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 17.01.19
 * Time: 09:06
 */

namespace Pyz\Zed\HeidelpayRest\Business;

use heidelpayPHP\Heidelpay;
use heidelpayPHP\Interfaces\DebugHandlerInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Pyz\Zed\HeidelpayRest\Business\Debug\DebugHandler;
use Pyz\Zed\HeidelpayRest\Business\Debug\DebugLogConfig;
use Pyz\Zed\HeidelpayRest\Business\Debug\DebugLogFormatter;
use Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPayment;
use Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface;
use Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentLog;
use Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentLogInterface;
use Pyz\Zed\HeidelpayRest\Business\Order\Saver;
use Pyz\Zed\HeidelpayRest\Business\Order\SaverInterface;
use Pyz\Zed\HeidelpayRest\Business\PaymentType\CardType;
use Pyz\Zed\HeidelpayRest\Business\PaymentType\CardTypeInterface;
use Pyz\Zed\HeidelpayRest\Business\PaymentType\PaymentType;
use Pyz\Zed\HeidelpayRest\Business\PaymentType\PaymentTypeInterface;
use Pyz\Zed\HeidelpayRest\Business\PaymentType\PayPalType;
use Pyz\Zed\HeidelpayRest\Business\PaymentType\PayPalTypeInterface;
use Pyz\Zed\HeidelpayRest\Business\PaymentType\SepaType;
use Pyz\Zed\HeidelpayRest\Business\PaymentType\SepaTypeInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Authorize;
use Pyz\Zed\HeidelpayRest\Business\Transaction\AuthorizeInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Cancel;
use Pyz\Zed\HeidelpayRest\Business\Transaction\CancelInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Capture;
use Pyz\Zed\HeidelpayRest\Business\Transaction\CaptureInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Finalize;
use Pyz\Zed\HeidelpayRest\Business\Transaction\FinalizeInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Log\Logger as TransactionLogger;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\MetaData\BillingPeriod;
use Pyz\Zed\HeidelpayRest\Business\Transaction\MetaData\BillingPeriodInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Refund;
use Pyz\Zed\HeidelpayRest\Business\Transaction\RefundInterface;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Resource\Customer;
use Pyz\Zed\HeidelpayRest\Business\Transaction\Resource\CustomerInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapper;
use Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface;
use Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtil;
use Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface;
use Pyz\Zed\HeidelpayRest\Business\Validation\CustomerValidator;
use Pyz\Zed\HeidelpayRest\Business\Validation\CustomerValidatorInterface;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToMoneyBridgeInterface;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToOmsBridgeInterface;
use Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface;
use Pyz\Zed\HeidelpayRest\HeidelpayRestDependencyProvider;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Mail\Business\MailFacadeInterface;

/**
 * Class HeidelpayRestBusinessFactory
 * @package Pyz\Zed\HeidelpayRest\Business
 * @method \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig getConfig()
 * @method \Pyz\Zed\HeidelpayRest\Persistence\HeidelpayRestQueryContainerInterface getQueryContainer()
 */
class HeidelpayRestBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Transaction\AuthorizeInterface
     */
    public function createAuthorizeTransaction(): AuthorizeInterface
    {
        return new Authorize(
            $this->createClientWrapper(),
            $this->getConfig(),
            $this->createMoneyUtil(),
            $this->createPaymentType(),
            $this->createHeidelpayRestPayment(),
            $this->createTransactionLogger(),
            $this->getOmsFacade(),
            $this->getSalesFacade(),
            $this->createCustomer(),
            $this->createBillingPeriodMetaData()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\PaymentType\PaymentTypeInterface
     */
    public function createPaymentType(): PaymentTypeInterface
    {
        return new PaymentType(
            $this
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Transaction\CaptureInterface
     */
    public function createCaptureTransaction(): CaptureInterface
    {
        return new Capture(
            $this->createHeidelpayRestPayment(),
            $this->createClientWrapper(),
            $this->createMoneyUtil(),
            $this->createTransactionLogger(),
            $this->getSalesFacade(),
            $this->getConfig(),
            $this->createCustomer(),
            $this->createBillingPeriodMetaData()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Transaction\RefundInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createRefundTransaction(): RefundInterface
    {
        return new Refund(
            $this->createClientWrapper(),
            $this->createTransactionLogger(),
            $this->createMoneyUtil(),
            $this->getConfig(),
            $this->getSalesFacade(),
            $this->getMailFacade()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Transaction\CancelInterface
     */
    public function createCancelTransaction(): CancelInterface
    {
        return new Cancel(
            $this->createHeidelpayRestPayment(),
            $this->createClientWrapper(),
            $this->createTransactionLogger(),
            $this->createMoneyUtil(),
            $this->getSalesFacade()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Order\SaverInterface
     */
    public function createOrderSaver(): SaverInterface
    {
        return new Saver(
            $this->createHeidelpayRestPayment()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\PaymentType\CardTypeInterface
     */
    public function createCardPaymentType(): CardTypeInterface
    {
        return new CardType(
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\PaymentType\PayPalTypeInterface
     */
    public function createPayPalPaymentType(): PayPalTypeInterface
    {
        return new PayPalType(
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\PaymentType\SepaTypeInterface
     */
    public function createSepaType(): SepaTypeInterface
    {
        return new SepaType(
            $this->createHeidelpayClient()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Transaction\FinalizeInterface
     */
    public function createFinalizeTransaction(): FinalizeInterface
    {
        return new Finalize(
            $this->createHeidelpayRestPayment(),
            $this->createClientWrapper(),
            $this->createTransactionLogger(),
            $this->getConfig(),
            $this->createMoneyUtil(),
            $this->getSalesFacade()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Transaction\MetaData\BillingPeriodInterface
     */
    public function createBillingPeriodMetaData(): BillingPeriodInterface
    {
        return new BillingPeriod(
            $this->createClientWrapper(),
            $this->getBillingFacade(),
            $this->createTransactionLogger(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Validation\CustomerValidatorInterface
     */
    public function createCustomerValidator(): CustomerValidatorInterface
    {
        return new CustomerValidator(
            $this->createClientWrapper(),
            $this->getConfig(),
            $this->getSalesFacade()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Util\ClientWrapperInterface
     */
    protected function createClientWrapper(): ClientWrapperInterface
    {
        return new ClientWrapper(
            $this->createHeidelpayClient()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Transaction\Resource\CustomerInterface
     */
    protected function createCustomer(): CustomerInterface
    {
        return new Customer(
            $this->createClientWrapper(),
            $this->createTransactionLogger()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentInterface
     */
    protected function createHeidelpayRestPayment(): HeidelpayRestPaymentInterface
    {
        return new HeidelpayRestPayment(
            $this->getQueryContainer()
        );
    }

    /**
     * @return \heidelpayPHP\Heidelpay
     */
    protected function createHeidelpayClient(): Heidelpay
    {
        $heidelpayClient = new Heidelpay(
            $this->getHeidelpayPrivateKey(),
            $this->getHeidelpayLocale()
        );

        if ($this->getIsDebug() === true) {
            $heidelpayClient->setDebugMode(true);
            $heidelpayClient->setDebugHandler($this->createDebugHandler());
        }

        return $heidelpayClient;
    }

    /**
     * @return \heidelpayPHP\Interfaces\DebugHandlerInterface
     */
    protected function createDebugHandler(): DebugHandlerInterface
    {
        return new DebugHandler(
            $this->createDebugLogConfig()
        );
    }

    /**
     * @return \Spryker\Shared\Log\Config\LoggerConfigInterface
     */
    protected function createDebugLogConfig(): LoggerConfigInterface
    {
        return new DebugLogConfig(
            [
                $this->createDebugLogStreamHandler(),
            ]
        );
    }

    /**
     * @return \Monolog\Handler\HandlerInterface
     */
    protected function createDebugLogStreamHandler(): HandlerInterface
    {
        $handler = new StreamHandler(
            $this->getConfig()->getDebugLogPath(),
            Logger::INFO
        );

        $handler->setFormatter($this->createDebugLogFormatter());

        return $handler;
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Debug\DebugLogFormatter
     */
    protected function createDebugLogFormatter(): DebugLogFormatter
    {
        return new DebugLogFormatter();
    }

    /**
     * @return bool
     */
    protected function getIsDebug(): bool
    {
        return $this
            ->getConfig()
            ->getIsDebug();
    }

    /**
     * @return string
     */
    protected function getHeidelpayPrivateKey(): string
    {
        return $this
            ->getConfig()
            ->getPrvateKey();
    }

    /**
     * @return string
     */
    protected function getHeidelpayLocale(): string
    {
        return $this
            ->getConfig()
            ->getLocale();
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToMoneyBridgeInterface
     */
    protected function getMoneyFacade(): HeidelpayRestToMoneyBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                HeidelpayRestDependencyProvider::FACADE_MONEY
            );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Util\MoneyUtilInterface
     */
    protected function createMoneyUtil(): MoneyUtilInterface
    {
        return new MoneyUtil(
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Transaction\Log\LoggerInterface
     */
    protected function createTransactionLogger(): LoggerInterface
    {
        return new TransactionLogger(
            $this->getQueryContainer(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToOmsBridgeInterface
     */
    protected function getOmsFacade(): HeidelpayRestToOmsBridgeInterface
    {
        return $this
            ->getProvidedDependency(HeidelpayRestDependencyProvider::FACADE_OMS);
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Dependency\Facade\HeidelpayRestToSalesBridgeInterface
     */
    protected function getSalesFacade(): HeidelpayRestToSalesBridgeInterface
    {
        return $this
            ->getProvidedDependency(HeidelpayRestDependencyProvider::FACADE_SALES);
    }

    /**
     * @return \Pyz\Zed\Billing\Business\BillingFacadeInterface
     */
    protected function getBillingFacade(): BillingFacadeInterface
    {
        return $this
            ->getProvidedDependency(HeidelpayRestDependencyProvider::FACADE_BILLING);
    }

    /**
     * @return \Spryker\Zed\Mail\Business\MailFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMailFacade(): MailFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                HeidelpayRestDependencyProvider::FACADE_MAIL
            );
    }

    /**
     * @return \Pyz\Zed\HeidelpayRest\Business\Model\HeidelpayRestPaymentLogInterface
     */
    public function createHeidelpayRestPaymentLog(): HeidelpayRestPaymentLogInterface
    {
        return new HeidelpayRestPaymentLog(
            $this->getQueryContainer()
        );
    }
}
