<?php

namespace Pyz\Zed\DeliveryArea\Business;

use Pyz\Zed\Absence\Business\AbsenceFacadeInterface;
use Pyz\Zed\DeliveryArea\Business\Calculator\DeliveryCostCalculator;
use Pyz\Zed\DeliveryArea\Business\Calculator\DeliveryCostTaxRateCalculator;
use Pyz\Zed\DeliveryArea\Business\Calculator\MissingMinUnitsCalculator;
use Pyz\Zed\DeliveryArea\Business\Calculator\MissingMinValueCalculator;
use Pyz\Zed\DeliveryArea\Business\Checkout\ConcreteTimeSlotTouchPostSaveHook;
use Pyz\Zed\DeliveryArea\Business\Checkout\DeliveryCostOrderSaver;
use Pyz\Zed\DeliveryArea\Business\Creator\ConcreteTimeSlotCreator;
use Pyz\Zed\DeliveryArea\Business\Creator\ConcreteTimeSlotCreatorInterface;
use Pyz\Zed\DeliveryArea\Business\Creator\ConcreteTimeSlotDeleteToucher;
use Pyz\Zed\DeliveryArea\Business\Creator\ConcreteTimeSlotDeleteToucherInterface;
use Pyz\Zed\DeliveryArea\Business\Creator\PassedConcreteTimeSlotDeleteToucher;
use Pyz\Zed\DeliveryArea\Business\Creator\PassedConcreteTimeSlotDeleteToucherInterface;
use Pyz\Zed\DeliveryArea\Business\Discount\DeliveryAreaCollector;
use Pyz\Zed\DeliveryArea\Business\Discount\DeliveryAreaCollectorInterface;
use Pyz\Zed\DeliveryArea\Business\Discount\DeliveryAreaDecisionRule;
use Pyz\Zed\DeliveryArea\Business\Discount\DeliveryAreaDecisionRuleInterface;
use Pyz\Zed\DeliveryArea\Business\Export\CsvTimeSlotExporter;
use Pyz\Zed\DeliveryArea\Business\Export\CsvTimeSlotExporterInterface;
use Pyz\Zed\DeliveryArea\Business\Finder\TimeSlotFinder;
use Pyz\Zed\DeliveryArea\Business\Finder\TimeSlotSorter;
use Pyz\Zed\DeliveryArea\Business\Hydrator\ConcreteTimeSlotOrderHydrator;
use Pyz\Zed\DeliveryArea\Business\Import\TimeSlotCsvImporter;
use Pyz\Zed\DeliveryArea\Business\Import\TimeSlotCsvImporterInterface;
use Pyz\Zed\DeliveryArea\Business\Manager\MinUnitsExpander;
use Pyz\Zed\DeliveryArea\Business\Manager\MinValueExpander;
use Pyz\Zed\DeliveryArea\Business\Manager\TimeSlotManager;
use Pyz\Zed\DeliveryArea\Business\Map\DeliveryAreaDataPageMapBuilder;
use Pyz\Zed\DeliveryArea\Business\Map\TimeslotDataPageMapBuilder;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\AbsenceAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\ActiveAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\MaxCustomersAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\MaxPayloadAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\MaxProductsAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\PrepTimeAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\AssertionChecker;
use Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlot;
use Pyz\Zed\DeliveryArea\Business\Model\ConcreteTimeSlotAssertionInterface;
use Pyz\Zed\DeliveryArea\Business\Model\DeliveryArea;
use Pyz\Zed\DeliveryArea\Business\Model\TimeSlot;
use Pyz\Zed\DeliveryArea\Business\Repository\DeliveryAreaRepository;
use Pyz\Zed\DeliveryArea\Business\Writer\ConcreteTimeSlotWriter;
use Pyz\Zed\DeliveryArea\Business\Writer\DeliveryAreaWriter;
use Pyz\Zed\DeliveryArea\Business\Writer\TimeSlotWriter;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotDeletePluginInterface;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotSavePluginInterface;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostDeliveryAreaSavePluginInterface;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;
use Pyz\Zed\DeliveryArea\DeliveryAreaDependencyProvider;
use Pyz\Zed\DeliveryArea\Dependency\Facade\DeliveryAreaToTouchBridgeInterface;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainer;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Client\Queue\QueueClientInterface;
use Spryker\Shared\ErrorHandler\ErrorLogger;
use Spryker\Shared\ErrorHandler\ErrorLoggerInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Mail\Business\MailFacadeInterface;
use Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Tax\Business\TaxFacadeInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @method DeliveryAreaConfig getConfig()
 * @method DeliveryAreaQueryContainer getQueryContainer()
 */
class DeliveryAreaBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return TimeSlotFinder
     */
    public function createTimeSlotFinder(): TimeSlotFinder
    {
        return new TimeSlotFinder(
            $this->createAssertionChecker(),
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->getConcreteTimeSlotPostSavePlugins(),
            $this->getConcreteTimeSlotPostDeletePlugins(),
            $this->createTimeSlotModel(),
            $this->getSalesQueryContainer()
        );
    }

    /**
     * @return ConcreteTimeSlotCreatorInterface
     */
    public function createConcreteTimeSlotCreator(): ConcreteTimeSlotCreatorInterface
    {
        return new ConcreteTimeSlotCreator(
            $this->getConfig(),
            $this->createTimeSlotModel(),
            $this->getQueryContainer(),
            $this->getTouchFacade(),
            $this->getTourFacade(),
            $this->createAssertionChecker(),
            $this->getSoftwarePackageFacade(),
            $this->getIntegraFacade()
        );
    }

    /**
     * @return MinValueExpander
     */
    public function createMinValueExpander(): MinValueExpander
    {
        return new MinValueExpander();
    }

    /**
     * @return MinUnitsExpander
     */
    public function createMinUnitsExpander(): MinUnitsExpander
    {
        return new MinUnitsExpander($this->createTimeSlotModel());
    }

    /**
     * @return MissingMinValueCalculator
     */
    public function createMissingMinValueCalculator(): MissingMinValueCalculator
    {
        return new MissingMinValueCalculator();
    }

    /**
     * @return MissingMinUnitsCalculator
     */
    public function createMissingMinUnitsCalculator(): MissingMinUnitsCalculator
    {
        return new MissingMinUnitsCalculator();
    }

    /**
     * @return DeliveryArea
     */
    public function createDeliveryAreaModel()
    {
        return new DeliveryArea(
            $this->getQueryContainer(),
            $this->getPostDeliveryAreaSavePlugins(),
            $this->getPostDeliveryAreaDeletePlugins(),
            $this->getMerchantFacade(),
            $this->getGraphMastersFacade()
        );
    }

    /**
     * @return TimeSlot
     */
    public function createTimeSlotModel()
    {
        return new TimeSlot(
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->getConcreteTimeSlotPostSavePlugins(),
            $this->getConcreteTimeSlotPostDeletePlugins()
        );
    }

    /**
     * @return TimeSlotSorter
     */
    protected function createTimeSlotSorter(): TimeSlotSorter
    {
        return new TimeSlotSorter();
    }

    /**
     * @return DeliveryAreaWriter
     */
    public function createDeliveryAreaWriter()
    {
        return new DeliveryAreaWriter($this->getQueryContainer());
    }

    /**
     * @return TimeSlotWriter
     */
    public function createTimeSlotWriter()
    {
        return new TimeSlotWriter($this->getQueryContainer());
    }

    /**
     * @return ConcreteTimeSlotWriter
     */
    public function createConcreteTimeSlotWriter()
    {
        return new ConcreteTimeSlotWriter($this->getQueryContainer());
    }

    /**
     * @return AssertionChecker
     */
    public function createAssertionChecker(): AssertionChecker
    {
        return new AssertionChecker(
            $this->createAssertionStack()
        );
    }

    /**
     * @return ConcreteTimeSlot
     */
    public function createConcreteTimeSlotModel()
    {
        return new ConcreteTimeSlot(
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->createAssertionChecker(),
            $this->createMaxPayloadAssertion(),
            $this->createMaxProductsAssertion(),
            $this->getConcreteTimeSlotPostSavePlugins(),
            $this->getConcreteTimeSlotPostDeletePlugins(),
            $this->getTourFacade()
        );
    }

    /**
     * @return TimeSlotManager
     */
    public function createTimeSlotManager()
    {
        return new TimeSlotManager(
            $this->getQueryContainer()
        );
    }

    /**
     * @return DeliveryCostOrderSaver
     */
    public function createDeliveryCostOrderSaver(): DeliveryCostOrderSaver
    {
        return new DeliveryCostOrderSaver(
            $this->getSalesQueryContainer()
        );
    }

    /**
     * @return AbsenceFacadeInterface
     */
    protected function getAbsenceFacade(): AbsenceFacadeInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::FACADE_ABSENCE);
    }

    /**
     * @return array
     */
    protected function createAssertionStack(): array
    {
        return [
            $this->createMaxCustomersAssertion(),
            //$this->createMaxProductsAssertion(),
            $this->createPrepTimeAssertion(),
            $this->createAbsenceAssertion(),
            //$this->createMaxPayloadAssertion(),
            $this->createActiveAssertion(),
        ];
    }

    /**
     * @return DeliveryCostCalculator
     */
    public function createDeliveryCostCalculator(): DeliveryCostCalculator
    {
        return new DeliveryCostCalculator();
    }

    /**
     * @return DeliveryCostTaxRateCalculator
     */
    public function createDeliveryCostTaxRateCalculator()
    {
        return new DeliveryCostTaxRateCalculator(
            $this->getTaxFacade()
        );
    }

    /**
     * @return CsvTimeSlotExporterInterface
     */
    public function createCsvTimeSlotExporter(): CsvTimeSlotExporterInterface
    {
        return new CsvTimeSlotExporter(
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->getFilesystem(),
            $this->getMailFacade(),
            $this->getQueueClient(),
            $this->getMerchantFacade()
        );
    }

    /**
     * @return MaxCustomersAssertion
     */
    protected function createMaxCustomersAssertion(): MaxCustomersAssertion
    {
        return new MaxCustomersAssertion(
            $this->getConfig()
        );
    }

    /**
     * @return MaxProductsAssertion
     */
    protected function createMaxProductsAssertion(): MaxProductsAssertion
    {
        return new MaxProductsAssertion(
            $this->getConfig()
        );
    }

    /**
     * @return AbsenceAssertion
     */
    protected function createAbsenceAssertion(): AbsenceAssertion
    {
        return new AbsenceAssertion(
            $this->getAbsenceFacade()
        );
    }

    /**
     * @return PrepTimeAssertion
     */
    protected function createPrepTimeAssertion(): PrepTimeAssertion
    {
        return new PrepTimeAssertion();
    }

    /**
     * @return MaxPayloadAssertion
     */
    protected function createMaxPayloadAssertion(): MaxPayloadAssertion
    {
        return new MaxPayloadAssertion(
            $this->getDepositFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return ConcreteTimeSlotAssertionInterface|ActiveAssertion
     */
    protected function createActiveAssertion(): ConcreteTimeSlotAssertionInterface
    {
        return new ActiveAssertion();
    }

    /**
     * @return SalesQueryContainerInterface
     */
    protected function getSalesQueryContainer(): SalesQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::QUERY_CONTAINER_SALES);
    }

    /**
     * @return ConcreteTimeSlotOrderHydrator
     */
    public function createConcreteTimeSlotHydrator(): ConcreteTimeSlotOrderHydrator
    {
        return new ConcreteTimeSlotOrderHydrator(
            $this->createConcreteTimeSlotModel()
        );
    }

    /**
     * @return PassedConcreteTimeSlotDeleteToucherInterface
     */
    public function createPassedConcreteTimeSlotDeleteToucher(): PassedConcreteTimeSlotDeleteToucherInterface
    {
        return new PassedConcreteTimeSlotDeleteToucher(
            $this->getTouchFacade(),
            $this->getQueryContainer()
        );
    }

    /**
     * @return DeliveryAreaCollectorInterface
     */
    public function createDeliveryAreaCollector(): DeliveryAreaCollectorInterface
    {
        return new DeliveryAreaCollector(
            $this->createDeliveryAreaDecisionRule()
        );
    }

    /**
     * @return DeliveryAreaDecisionRuleInterface
     */
    public function createDeliveryAreaDecisionRule(): DeliveryAreaDecisionRuleInterface
    {
        return new DeliveryAreaDecisionRule(
            $this->getDiscountFacade()
        );
    }

    /**
     * @return TaxFacadeInterface
     */
    protected function getTaxFacade(): TaxFacadeInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::FACADE_TAX);
    }

    /**
     * @return TourFacadeInterface
     */
    protected function getTourFacade(): TourFacadeInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::FACADE_TOUR);
    }

    /**
     * @return DeliveryAreaToTouchBridgeInterface
     */
    protected function getTouchFacade(): DeliveryAreaToTouchBridgeInterface
    {
        return $this->getProvidedDependency(DeliveryAreaDependencyProvider::FACADE_TOUCH);
    }

    /**
     * @return DeliveryAreaDataPageMapBuilder
     */
    public function createDeliveryAreaDataPageMapBuilder(): DeliveryAreaDataPageMapBuilder
    {
        return new DeliveryAreaDataPageMapBuilder();
    }

    /**
     * @return TimeslotDataPageMapBuilder
     */
    public function createTimeslotDataPageMapBuilder(): TimeslotDataPageMapBuilder
    {
        return new TimeslotDataPageMapBuilder();
    }

    /**
     * @return PostDeliveryAreaSavePluginInterface[]
     */
    protected function getPostDeliveryAreaSavePlugins(): array
    {
        return $this->getProvidedDependency(DeliveryAreaDependencyProvider::POST_DELIVERY_AREA_SAVE_PLUGINS);
    }

    /**
     * @return PostDeliveryAreaSavePluginInterface[]
     */
    protected function getPostDeliveryAreaDeletePlugins(): array
    {
        return $this->getProvidedDependency(DeliveryAreaDependencyProvider::POST_DELIVERY_AREA_DELETE_PLUGINS);
    }

    /**
     * @return PostConcreteTimeSlotSavePluginInterface[]
     */
    protected function getConcreteTimeSlotPostSavePlugins(): array
    {
        return $this->getProvidedDependency(DeliveryAreaDependencyProvider::POST_CONCRETE_TIME_SLOT_SAVE_PLUGINS);
    }

    /**
     * @return PostConcreteTimeSlotDeletePluginInterface[]
     */
    protected function getConcreteTimeSlotPostDeletePlugins(): array
    {
        return $this->getProvidedDependency(DeliveryAreaDependencyProvider::POST_CONCRETE_TIME_SLOT_DELETE_PLUGINS);
    }

    /**
     * @return DepositFacadeInterface
     */
    protected function getDepositFacade(): DepositFacadeInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::FACADE_DEPOSIT);
    }

    /**
     * @return QueueClientInterface
     */
    protected function getQueueClient(): QueueClientInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::CLIENT_QUEUE);
    }

    /**
     * @return MailFacadeInterface
     */
    protected function getMailFacade(): MailFacadeInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::FACADE_MAIL);
    }

    /**
     * @return Filesystem
     */
    protected function getFilesystem(): Filesystem
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::FILESYSTEM);
    }

    /**
     * @return ConcreteTimeSlotTouchPostSaveHook
     */
    public function createConcreteTimeSlotTouchPostSaveHook(): ConcreteTimeSlotTouchPostSaveHook
    {
        return new ConcreteTimeSlotTouchPostSaveHook(
            $this->getQueryContainer(),
            $this->getTouchFacade()
        );
    }

    /**
     * @return DiscountFacadeInterface
     */
    protected function getDiscountFacade(): DiscountFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                DeliveryAreaDependencyProvider::FACADE_DISCOUNT
            );
    }

    /**
     * @return ConcreteTimeSlotDeleteToucherInterface
     */
    public function createConcreteTimeSlotDeleteToucher(): ConcreteTimeSlotDeleteToucherInterface
    {
        return new ConcreteTimeSlotDeleteToucher(
            $this->getTouchFacade(),
            $this->getQueryContainer()
        );
    }

    /**
     * @return TimeSlotCsvImporterInterface
     */
    public function createTimeSlotCsvImporter(): TimeSlotCsvImporterInterface
    {
        return new TimeSlotCsvImporter(
            $this->getConfig(),
            $this->getQueryContainer(),
            $this->createTimeSlotModel(),
            $this->createDeliveryAreaRepository(),
            $this->getMailFacade(),
            $this->getMerchantFacade(),
            $this->getErrorLogger()
        );
    }

    /**
     * @return MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return DeliveryAreaRepository
     */
    protected function createDeliveryAreaRepository() : DeliveryAreaRepository
    {
        return new DeliveryAreaRepository();
    }

    protected function getErrorLogger(): ErrorLoggerInterface
    {
        return ErrorLogger::getInstance();
    }

    /**
     * @return SoftwarePackageFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getSoftwarePackageFacade(): SoftwarePackageFacadeInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::FACADE_SOFTWARE_PACKAGE);
    }

    /**
     * @return IntegraFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getIntegraFacade(): IntegraFacadeInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::FACADE_INTEGRA);
    }

    /**
     * @return GraphMastersFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getGraphMastersFacade() : GraphMastersFacadeInterface
    {
        return $this
            ->getProvidedDependency(DeliveryAreaDependencyProvider::FACADE_GRAPHMASTERS);
    }
}
