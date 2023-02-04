<?php

namespace Pyz\Zed\Integra\Business;

use Pyz\Service\SoapRequest\SoapRequestServiceInterface;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Pyz\Zed\Integra\Business\Model\Connection\FtpManager;
use Pyz\Zed\Integra\Business\Model\Connection\FtpManagerInterface;
use Pyz\Zed\Integra\Business\Model\Connection\WebServiceManager;
use Pyz\Zed\Integra\Business\Model\Connection\WebServiceManagerInterface;
use Pyz\Zed\Integra\Business\Model\DeliveryNoteManager;
use Pyz\Zed\Integra\Business\Model\DeliveryNoteManagerInterface;
use Pyz\Zed\Integra\Business\Model\Encryption\PasswordManager;
use Pyz\Zed\Integra\Business\Model\Encryption\PasswordManagerInterface;
use Pyz\Zed\Integra\Business\Model\Export\ClosedOrdersExport;
use Pyz\Zed\Integra\Business\Model\Export\OpenOrdersExport;
use Pyz\Zed\Integra\Business\Model\ExportManager;
use Pyz\Zed\Integra\Business\Model\ExportManagerInterface;
use Pyz\Zed\Integra\Business\Model\ImportManager;
use Pyz\Zed\Integra\Business\Model\ImportManagerInterface;
use Pyz\Zed\Integra\Business\Model\IntegraCredentials;
use Pyz\Zed\Integra\Business\Model\IntegraCredentialsInterface;
use Pyz\Zed\Integra\Business\Model\Log\Logger;
use Pyz\Zed\Integra\Business\Model\Log\LoggerInterface;
use Pyz\Zed\Integra\Business\Model\Order\OrderUpdater;
use Pyz\Zed\Integra\Business\Model\Order\OrderUpdaterInterface;
use Pyz\Zed\Integra\Business\Model\Quote\AddressHydrator;
use Pyz\Zed\Integra\Business\Model\Quote\AddressHydratorInterface;
use Pyz\Zed\Integra\Business\Model\Quote\CustomerHydrator;
use Pyz\Zed\Integra\Business\Model\Quote\CustomerHydratorInterface;
use Pyz\Zed\Integra\Business\Model\Quote\Deposit\DepositRepository;
use Pyz\Zed\Integra\Business\Model\Quote\Deposit\DepositRepositoryInterface;
use Pyz\Zed\Integra\Business\Model\Quote\ExpensesHydrator;
use Pyz\Zed\Integra\Business\Model\Quote\ExpensesHydratorInterface;
use Pyz\Zed\Integra\Business\Model\Quote\ItemsHydrator;
use Pyz\Zed\Integra\Business\Model\Quote\ItemsHydratorInterface;
use Pyz\Zed\Integra\Business\Model\Quote\PaymentHydrator;
use Pyz\Zed\Integra\Business\Model\Quote\PaymentHydratorInterface;
use Pyz\Zed\Integra\Business\Model\Quote\Product\ProductRepository;
use Pyz\Zed\Integra\Business\Model\Quote\Product\ProductRepositoryInterface;
use Pyz\Zed\Integra\Business\Model\Quote\QuoteHydrator;
use Pyz\Zed\Integra\Business\Model\Quote\QuoteHydratorInterface;
use Pyz\Zed\Integra\Business\Model\Quote\TotalsHydrator;
use Pyz\Zed\Integra\Business\Model\Quote\TotalsHydratorInterface;
use Pyz\Zed\Integra\Business\Model\TimeSlot\ConcreteTimeSlotRepository;
use Pyz\Zed\Integra\Business\Model\TimeSlot\ConcreteTimeSlotRepositoryInterface;
use Pyz\Zed\Integra\Business\Model\TimeSlot\ConcreteTourRepository;
use Pyz\Zed\Integra\Business\Model\TimeSlot\ConcreteTourRepositoryInterface;
use Pyz\Zed\Integra\IntegraConfig;
use Pyz\Zed\Integra\IntegraDependencyProvider;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainer;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Pyz\Zed\Pdf\Business\PdfFacadeInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;
use Spryker\Zed\Currency\Business\CurrencyFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @method IntegraConfig getConfig()
 * @method IntegraQueryContainer getQueryContainer()
 */
class IntegraBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return IntegraCredentialsInterface
     * @var DepositRepositoryInterface
     */
    protected $depositRepo;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepo;

    /**
     * @var ConcreteTourRepositoryInterface
     */
    protected $tourRepo;

    /**
     * @var ConcreteTimeSlotRepositoryInterface
     */
    protected $timeSlotRepo;

    /**
     * @return IntegraCredentialsInterface
     */
    public function createIntegraCredentialsModel(): IntegraCredentialsInterface
    {
        return new IntegraCredentials(
            $this->getQueryContainer(),
            $this->createPasswordManager()
        );
    }

    /**
     * @return ExportManagerInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createOpenOrdersExportManager(): ExportManagerInterface
    {
        return new ExportManager(
            $this->getConfig(),
            $this->createFilesystem(),
            $this->createIntegraCredentialsModel(),
            $this->createFtpManager(),
            $this->createOpenOrdersExport(),
            $this->createDeliveryNoteManager()
        );
    }

    /**
     * @return ExportManagerInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createClosedOrdersExportManager(): ExportManagerInterface
    {
        return new ExportManager(
            $this->getConfig(),
            $this->createFilesystem(),
            $this->createIntegraCredentialsModel(),
            $this->createFtpManager(),
            $this->createClosedOrdersExport(),
            $this->createDeliveryNoteManager()
        );
    }

    /**
     * @return ImportManagerInterface
     */
    public function createImportManager(): ImportManagerInterface
    {
        return new ImportManager(
            $this->getSalesFacade(),
            $this->createWebServiceManager(),
            //new WebServiceManagerMock(),
            $this->createIntegraCredentialsModel(),
            $this->createConcreteTimeSlotRepository(),
            $this->createTourRepository(),
            $this->createQuoteHydrator(),
            $this->createOrderUpdater(),
            $this->createLogger(),
            $this->getConfig(),
            $this->getQueryContainer(),
            $this->getOmsFacade(),
            $this->getDepositFacade()
        );
    }

    /**
     * @return ConcreteTourRepositoryInterface
     */
    protected function createTourRepository(): ConcreteTourRepositoryInterface
    {
        if($this->tourRepo === null){
            $this->tourRepo = new ConcreteTourRepository(
                $this->getQueryContainer()
            );
        }

        return $this->tourRepo;
    }

    /**
     * @return QuoteHydratorInterface
     */
    protected function createQuoteHydrator(): QuoteHydratorInterface
    {
        return new QuoteHydrator(
            $this->getCurrencyFacade(),
            $this->createAddressHydrator(),
            $this->createPaymentHydrator(),
            $this->createTotalsHydrator(),
            $this->createCustomerHydrator(),
            $this->createItemsHydrator(),
            $this->createExpensesHydrator(),
            $this->getDepositRepository(),
            $this->getProductRepository()
        );
    }

    /**
     * @return ExpensesHydratorInterface
     */
    protected function createExpensesHydrator(): ExpensesHydratorInterface
    {
        return new ExpensesHydrator(
            $this->getDepositRepository(),
            $this->getTaxFacade(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return ItemsHydratorInterface
     */
    protected function createItemsHydrator(): ItemsHydratorInterface
    {
        return new ItemsHydrator(
            $this->getMoneyFacade(),
            $this->getDepositRepository()
        );
    }

    /**
     * @return DepositRepositoryInterface
     */
    protected function getDepositRepository(): DepositRepositoryInterface
    {
        if($this->depositRepo === null){
            $this->depositRepo = new DepositRepository(
                $this->getQueryContainer()
            );
        }

        return $this->depositRepo;
    }

    /**
     * @return ProductRepositoryInterface
     */
    protected function getProductRepository(): ProductRepositoryInterface
    {
        if($this->productRepo === null){
            $this->productRepo = new ProductRepository(
                $this->getQueryContainer()
            );
        }

        return $this->productRepo;
    }

    /**
     * @return CustomerHydratorInterface
     */
    protected function createCustomerHydrator(): CustomerHydratorInterface
    {
        return new CustomerHydrator();
    }

    /**
     * @return TotalsHydratorInterface
     */
    protected function createTotalsHydrator(): TotalsHydratorInterface
    {
        return new TotalsHydrator();
    }

    /**
     * @return PaymentHydratorInterface
     */
    protected function createPaymentHydrator(): PaymentHydratorInterface
    {
        return new PaymentHydrator();
    }

    /**
     * @return AddressHydratorInterface
     */
    protected function createAddressHydrator(): AddressHydratorInterface
    {
        return new AddressHydrator();
    }

    /**
     * @return ConcreteTimeSlotRepositoryInterface
     */
    protected function createConcreteTimeSlotRepository(): ConcreteTimeSlotRepositoryInterface
    {
        if($this->timeSlotRepo === null){
            $this->timeSlotRepo = new ConcreteTimeSlotRepository(
                $this->getQueryContainer()
            );
        }

        return $this->timeSlotRepo;
    }

    /**
     * @return OpenOrdersExport
     */
    protected function createOpenOrdersExport(): OpenOrdersExport
    {
        return new OpenOrdersExport(
            $this->getQueryContainer()
        );
    }

    /**
     * @return ClosedOrdersExport
     */
    protected function createClosedOrdersExport(): ClosedOrdersExport
    {
        return new ClosedOrdersExport(
            $this->getQueryContainer()
        );
    }

    /**
     * @return FtpManagerInterface
     */
    protected function createFtpManager(): FtpManagerInterface
    {
        return new FtpManager();
    }

    /**
     * @return Filesystem
     */
    protected function createFilesystem(): Filesystem
    {
        return new Filesystem();
    }

    /**
     * @return PasswordManagerInterface
     */
    protected function createPasswordManager(): PasswordManagerInterface
    {
        return new PasswordManager(
            $this->getConfig()
        );
    }

    /**
     * @return LoggerInterface
     */
    protected function createLogger(): LoggerInterface
    {
        return new Logger(
            $this->getQueryContainer(),
            $this->getConfig()
        );
    }

    /**
     * @return MoneyFacadeInterface
     */
    protected function getMoneyFacade(): MoneyFacadeInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::FACADE_MONEY);
    }

    /**
     * @return SoapRequestServiceInterface
     */
    protected function getSoapRequestService(): SoapRequestServiceInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::SERVICE_SOAP_REQUEST);
    }

    /**
     * @return WebServiceManagerInterface
     */
    protected function createWebServiceManager(): WebServiceManagerInterface
    {
        return new WebServiceManager(
            $this->getSoapRequestService()
        );
    }

    /**
     * @return SalesFacadeInterface
     */
    protected function getSalesFacade(): SalesFacadeInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::FACADE_SALES);
    }

    /**
     * @return CurrencyFacadeInterface
     */
    protected function getCurrencyFacade(): CurrencyFacadeInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::FACADE_CURRENCY);
    }

    /**
     * @return TaxFacadeInterface
     */
    protected function getTaxFacade(): TaxFacadeInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::FACADE_TAX);
    }

    /**
     * @return OrderUpdaterInterface
     */
    protected function createOrderUpdater(): OrderUpdaterInterface
    {
        return new OrderUpdater();
    }

    /**
     * @return OmsFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getOmsFacade() : OmsFacadeInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::FACADE_OMS);
    }

    /**
     * @return DepositFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getDepositFacade() : DepositFacadeInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::FACADE_DEPOSIT);
    }

    /**
     * @return DeliveryNoteManagerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function createDeliveryNoteManager() : DeliveryNoteManagerInterface
    {
        return new DeliveryNoteManager(
            $this->getConfig(),
            $this->getPdfFacade(),
            $this->getSalesFacade(),
            $this->getOmsFacade(),
            $this->getFileSystem(),
            $this->getMerchantQueryContainer()
        );
    }

    /**
     * @return PdfFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getPdfFacade() : PdfFacadeInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::FACADE_PDF);
    }

    /**
     * @return Filesystem
     * @throws ContainerKeyNotFoundException
     */
    protected function getFileSystem() : Filesystem
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::FILE_SYSTEM);
    }

    /**
     * @return \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantQueryContainer() : MerchantQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::QUERY_CONTAINER_MERCHANT);
    }
}
