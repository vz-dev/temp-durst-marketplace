<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 12:39
 */

namespace Pyz\Zed\Tour\Business;

use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Pyz\Zed\Driver\Business\DriverFacadeInterface;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainer;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Refund\Business\RefundFacadeInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Pyz\Zed\Touch\Business\TouchFacadeInterface;
use Pyz\Zed\Tour\Business\Checkout\TimeSlotConditionChecker;
use Pyz\Zed\Tour\Business\Checkout\TimeSlotConditionCheckerInterface;
use Pyz\Zed\Tour\Business\Client\TourExportClient;
use Pyz\Zed\Tour\Business\Manager\EdiExportManager;
use Pyz\Zed\Tour\Business\Manager\EdiExportManagerInterface;
use Pyz\Zed\Tour\Business\Mapper\Product\ProductRepository;
use Pyz\Zed\Tour\Business\Mapper\Product\ProductRepositoryInterface;
use Pyz\Zed\Tour\Business\Mapper\TourDriverAppMapper;
use Pyz\Zed\Tour\Business\Mapper\TourDriverappMapperInterface;
use Pyz\Zed\Tour\Business\Mapper\TourExportMapper;
use Pyz\Zed\Tour\Business\Mapper\TourToGraphhopperMapper;
use Pyz\Zed\Tour\Business\Model\AbstractTour;
use Pyz\Zed\Tour\Business\Model\AbstractTourHydrator\AbstractTimeSlotAbstractTourHydrator;
use Pyz\Zed\Tour\Business\Model\AbstractTourHydrator\AbstractTourHydratorInterface;
use Pyz\Zed\Tour\Business\Model\AbstractTourHydrator\DeliveryAreaAbstractTourHydrator;
use Pyz\Zed\Tour\Business\Model\AbstractTourHydrator\VehicleTypeAbstractTourHydrator as TourVehicleTypeHydrator;
use Pyz\Zed\Tour\Business\Model\AbstractTourInterface;
use Pyz\Zed\Tour\Business\Model\ConcreteTour;
use Pyz\Zed\Tour\Business\Model\ConcreteTourExport;
use Pyz\Zed\Tour\Business\Model\ConcreteTourExportInterface;
use Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator\AbstractTourConcreteTourHydrator;
use Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator\AvailableDriverConcreteTourHydrator;
use Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator\ConcreteTourHydratorInterface;
use Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator\DriverConcreteTourHydrator;
use Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator\SalesConcreteTourHydrator;
use Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator\StartAndEndTimeConcreteTourHydrator;
use Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator\StateConcreteTourHydrator;
use Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator\StatusConcreteTourHydrator;
use Pyz\Zed\Tour\Business\Model\ConcreteTourInterface;
use Pyz\Zed\Tour\Business\Model\DrivingLicence;
use Pyz\Zed\Tour\Business\Model\DrivingLicenceInterface;
use Pyz\Zed\Tour\Business\Model\EdifactReferenceGenerator;
use Pyz\Zed\Tour\Business\Model\Saver\AbstractTimeSlotSaver;
use Pyz\Zed\Tour\Business\Model\Saver\AbstractTimeSlotSaverInterface;
use Pyz\Zed\Tour\Business\Model\StateMachineTourItemReader;
use Pyz\Zed\Tour\Business\Model\StateMachineTourItemReaderInterface;
use Pyz\Zed\Tour\Business\Model\StateMachineTourItemSaver;
use Pyz\Zed\Tour\Business\Model\StateMachineTourItemSaverInterface;
use Pyz\Zed\Tour\Business\Model\TourOrder;
use Pyz\Zed\Tour\Business\Model\TourOrderInterface;
use Pyz\Zed\Tour\Business\Model\TourReferenceGenerator;
use Pyz\Zed\Tour\Business\Model\Vehicle;
use Pyz\Zed\Tour\Business\Model\VehicleCategory;
use Pyz\Zed\Tour\Business\Model\VehicleCategoryInterface;
use Pyz\Zed\Tour\Business\Model\VehicleHydrator\DrivingLicenceHydrator;
use Pyz\Zed\Tour\Business\Model\VehicleHydrator\VehicleHydratorInterface;
use Pyz\Zed\Tour\Business\Model\VehicleHydrator\VehicleTypeHydrator as VehicleVehicleTypeHydrator;
use Pyz\Zed\Tour\Business\Model\VehicleType;
use Pyz\Zed\Tour\Business\Model\VehicleTypeInterface;
use Pyz\Zed\Tour\Business\Parser\TourExportParser;
use Pyz\Zed\Tour\Business\Sales\TourOrderHydrator;
use Pyz\Zed\Tour\Business\Sales\TourOrderHydratorInterface;
use Pyz\Zed\Tour\Business\Util\EdiDepositExportUtil;
use Pyz\Zed\Tour\Dependency\Facade\TourToStateMachineBridgeInterface;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Pyz\Zed\Tour\TourConfig;
use Pyz\Zed\Tour\TourDependencyProvider;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

/**
 * @method TourConfig getConfig()
 * @method TourQueryContainerInterface getQueryContainer()
 */
class TourBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return AbstractTourInterface
     */
    public function createAbstractTourModel(): AbstractTourInterface
    {
        return new AbstractTour(
            $this->getQueryContainer(),
            $this->getAbstractTourHydratorStack(),
            $this->createAbstractTimeSlotSaver(),
            $this->getTouchFacade(),
            $this->getStateMachineFacade()
        );
    }

    /**
     * @return ConcreteTourInterface
     */
    public function createConcreteTourModel(): ConcreteTourInterface
    {
        return new ConcreteTour(
            $this->getQueryContainer(),
            $this->createTourReferenceGenerator(),
            $this->getConfig(),
            $this->getConcreteTourHydratorStack(),
            $this->getDeliveryAreaFacade(),
            $this->getIntegraFacade(),
            $this->getStateMachineFacade()
        );
    }

    /**
     * @return TourReferenceGenerator
     */
    public function createTourReferenceGenerator()
    {
        $sequenceNumberSettings = $this->getConfig()->getTourReferenceDefaults();

        return new TourReferenceGenerator(
            $this->getSequenceNumberFacade(),
            $sequenceNumberSettings
        );
    }

    /**
     * @return AbstractTourHydratorInterface
     */
    public function createDeliveryAreaHydrator(): AbstractTourHydratorInterface
    {
        return new DeliveryAreaAbstractTourHydrator(
            $this->getDeliveryAreaFacade()
        );
    }

    /**
     * @return AbstractTourHydratorInterface
     */
    public function createAbstractTimeSlotHydrator(): AbstractTourHydratorInterface
    {
        return new AbstractTimeSlotAbstractTourHydrator();
    }

    /**
     * @return ConcreteTourHydratorInterface
     */
    public function createAbstractTourHydrator(): ConcreteTourHydratorInterface
    {
        return new AbstractTourConcreteTourHydrator($this->createAbstractTourModel());
    }

    /**
     * @return SalesConcreteTourHydrator
     */
    public function createSalesHydrator(): SalesConcreteTourHydrator
    {
        return new SalesConcreteTourHydrator(
            $this->getSalesFacade(),
            $this->getConfig(),
            $this->getQueryContainer()
        );
    }

    /**
     * @return StatusConcreteTourHydrator
     */
    public function createStatusHydrator(): StatusConcreteTourHydrator
    {
        return new StatusConcreteTourHydrator(
            $this->getConfig()
        );
    }

    /**
     * @return StartAndEndTimeConcreteTourHydrator
     */
    public function createStartAndEndTimeHydrator(): StartAndEndTimeConcreteTourHydrator
    {
        return new StartAndEndTimeConcreteTourHydrator(
            $this->getDeliveryAreaFacade()
        );
    }

    /**
     * @return AbstractTimeSlotSaverInterface
     */
    public function createAbstractTimeSlotSaver(): AbstractTimeSlotSaverInterface
    {
        return new AbstractTimeSlotSaver(
            $this->getQueryContainer()
        );
    }

    /**
     * @return Vehicle
     */
    public function createVehicleModel(): Vehicle
    {
        return new Vehicle(
            $this->getQueryContainer(),
            $this->getVehicleHydratorStack()
        );
    }

    /**
     * @return DrivingLicenceInterface
     */
    public function createDrivingLicenceModel(): DrivingLicenceInterface
    {
        return new DrivingLicence(
            $this->getQueryContainer()
        );
    }

    /**
     * @return VehicleHydratorInterface
     */
    protected function createDrivingLicenceHydrator(): VehicleHydratorInterface
    {
        return new DrivingLicenceHydrator(
            $this->createDrivingLicenceModel()
        );
    }

    /**
     * @return VehicleTypeInterface
     */
    public function createVehicleTypeModel(): VehicleTypeInterface
    {
        return new VehicleType(
            $this->getQueryContainer(),
            $this->getTouchFacade()
        );
    }

    /**
     * @return VehicleHydratorInterface
     */
    protected function createVehicleVehicleTypeHydrator(): VehicleHydratorInterface
    {
        return new VehicleVehicleTypeHydrator(
            $this->createVehicleTypeModel()
        );
    }

    /**
     * @return AbstractTourHydratorInterface
     */
    protected function createVehicleTypeHydrator(): AbstractTourHydratorInterface
    {
        return new TourVehicleTypeHydrator(
            $this->createVehicleTypeModel(),
            $this->createVehicleCategoryModel()
        );
    }

    /**
     * @return AbstractTourHydratorInterface[]
     */
    protected function getAbstractTourHydratorStack(): array
    {
        return [
            $this->createVehicleTypeHydrator(),
            $this->createAbstractTimeSlotHydrator(),
            $this->createDeliveryAreaHydrator(),
        ];
    }

    /**
     * @return array
     */
    protected function getConcreteTourHydratorStack(): array
    {
        return [
            $this->createAbstractTourHydrator(),
            $this->createSalesHydrator(),
            $this->createStatusHydrator(),
            $this->createStartAndEndTimeHydrator(),
            $this->createDriverConcreteTourHydrator(),
            $this->createAvailableDriverConcreteTourHydrator(),
            $this->createStateConcreteTourHydrator(),
        ];
    }

    /**
     * @return VehicleHydratorInterface[]
     */
    protected function getVehicleHydratorStack(): array
    {
        return [
            $this->createDrivingLicenceHydrator(),
            $this->createVehicleVehicleTypeHydrator(),
        ];
    }

    /**
     * @return VehicleCategoryInterface
     */
    public function createVehicleCategoryModel(): VehicleCategory
    {
        return new VehicleCategory($this->getQueryContainer());
    }

    /**
     * @return SalesFacadeInterface
     */
    protected function getSalesFacade(): SalesFacadeInterface
    {
        return $this->getProvidedDependency(TourDependencyProvider::FACADE_SALES);
    }

    /**
     * @return DeliveryAreaFacadeInterface
     */
    protected function getDeliveryAreaFacade()
    {
        return $this->getProvidedDependency(TourDependencyProvider::FACADE_DELIVERY_AREA);
    }

    /**
     * @return SequenceNumberFacadeInterface
     */
    protected function getSequenceNumberFacade()
    {
        return $this->getProvidedDependency(TourDependencyProvider::FACADE_SEQUENCE_NUMBER);
    }

    /**
     * @return TourExportMapper
     */
    public function createTourExportMapper(): TourExportMapper
    {
        return new TourExportMapper($this->getEdifactFacade());
    }

    /**
     * @param string $uri
     * @param array $options
     *
     * @return TourExportClient
     */
    public function createTourExportClient(string $uri, array $options): TourExportClient
    {
        return new TourExportClient(
            $uri,
            $options,
            $this->getEdifactFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return ConcreteTourExportInterface
     */
    public function createConcreteTourExportModel(): ConcreteTourExportInterface
    {
        return new ConcreteTourExport(
            $this->getQueryContainer(),
            $this->getConfig()
        );
    }

    /**
     * @return TourExportParser
     */
    public function createTourExportParser(): TourExportParser
    {
        return new TourExportParser(
            $this->getConfig(),
            $this->getEdifactFacade()
        );
    }

    /**
     * @return EdifactReferenceGenerator
     */
    public function createEdifactReferenceGenerator(): EdifactReferenceGenerator
    {
        $sequenceNumberSettings = $this
            ->getConfig()
            ->getEdifactReferenceDefaults();

        return new EdifactReferenceGenerator(
            $this->getSequenceNumberFacade(),
            $sequenceNumberSettings
        );
    }

    /**
     * @return EdiExportManagerInterface
     */
    public function createEdiExportManager(): EdiExportManagerInterface
    {
        return new EdiExportManager(
            $this->getConfig(),
            $this->getEdifactFacade()
        );
    }

    /**
     * @return TourDriverappMapperInterface
     */
    public function createTourDriverAppMapper(): TourDriverappMapperInterface
    {
        return new TourDriverAppMapper(
            $this->getMerchantFacade(),
            $this->createProductRepository(),
            $this->getConfig()
        );
    }

    /**
     * @return OmsQueryContainerInterface
     */
    protected function getOmsQueryContainer(): OmsQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::QUERY_CONTAINER_OMS);
    }

    /**
     * @return SalesQueryContainerInterface
     */
    protected function getSalesQueryContainer(): SalesQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::QUERY_CONTAINER_SALES);
    }

    /**
     * @param int $idTour
     * @param bool $isGraphmastersTour
     * @return EdiDepositExportUtil
     * @throws ContainerKeyNotFoundException
     */
    public function createEdiDepositExportUtil(int $idTour, bool $isGraphmastersTour = false): EdiDepositExportUtil
    {
        return new EdiDepositExportUtil(
            $idTour,
            $this->createConcreteTourModel(),
            $this->getConfig(),
            $this->getQueryContainer(),
            $this->getOmsQueryContainer(),
            $this->getSalesQueryContainer(),
            $this->createEdifactReferenceGenerator(),
            $this->getRefundFacade(),
            $this->getBillingFacade(),
            $this->getEdifactFacade(),
            $this->getGraphMastersQueryContainer(),
            $this->getGraphMastersFacade(),
            $isGraphmastersTour
        );
    }

    /**
     * @return GraphMastersQueryContainer
     * @throws ContainerKeyNotFoundException
     */
    public function getGraphMastersQueryContainer(): GraphMastersQueryContainer
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::QUERY_CONTAINER_GRAPHMASTERS);
    }

    /**
     * @return TourOrderHydratorInterface
     */
    public function createTourOrderHydrator(): TourOrderHydratorInterface
    {
        return new TourOrderHydrator(
            $this->getQueryContainer()
        );
    }

    /**
     * @return TouchFacadeInterface
     */
    protected function getTouchFacade(): TouchFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_TOUCH);
    }

    /**
     * @return TimeSlotConditionCheckerInterface
     */
    public function createTimeSlotConditionChecker(): TimeSlotConditionCheckerInterface
    {
        return new TimeSlotConditionChecker(
            $this->getDeliveryAreaFacade(),
            $this->getSoftwarePackageFacade(),
            $this->getMerchantFacade(),
            $this->createConcreteTourModel()
        );
    }

    /**
     * @return MerchantFacadeInterface
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return SoftwarePackageFacadeInterface
     */
    protected function getSoftwarePackageFacade(): SoftwarePackageFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_SOFTWARE_PACKAGE);
    }

    /**
     * @return RefundFacadeInterface
     */
    protected function getRefundFacade(): RefundFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_REFUND);
    }

    /**
     * @return TourOrderInterface
     */
    public function createTourOrderModel(): TourOrderInterface
    {
        return new TourOrder(
            $this->getQueryContainer(),
            $this->getOmsQueryContainer(),
            $this->getMerchantFacade(),
            $this->getSalesFacade(),
            $this->createConcreteTourModel(),
            $this->getConfig(),
            $this->createTourDriverAppMapper(),
            $this->getIntegraFacade(),
            $this->getDiscountQueryContainer()
        );
    }

    /**
     * @return ProductRepositoryInterface
     */
    protected function createProductRepository(): ProductRepositoryInterface
    {
        return new ProductRepository(
            $this->getQueryContainer()
        );
    }

    /**
     * @return EdifactFacadeInterface
     */
    protected function getEdifactFacade(): EdifactFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_EDIFACT);
    }

    /**
     * @return DriverFacadeInterface
     */
    protected function getDriverFacade(): DriverFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_DRIVER);
    }

    /**
     * @return ConcreteTourHydratorInterface
     */
    public function createDriverConcreteTourHydrator(): ConcreteTourHydratorInterface
    {
        return new DriverConcreteTourHydrator(
            $this->getDriverFacade()
        );
    }

    /**
     * @return ConcreteTourHydratorInterface
     */
    public function createAvailableDriverConcreteTourHydrator(): ConcreteTourHydratorInterface
    {
        return new AvailableDriverConcreteTourHydrator(
            $this->getQueryContainer(),
            $this->getDriverFacade()
        );
    }

    /**
     * @return StateMachineTourItemReaderInterface
     */
    public function createStateMachineTourItemReader(): StateMachineTourItemReaderInterface
    {
        return new StateMachineTourItemReader(
            $this->getQueryContainer()
        );
    }

    /**
     * @return StateMachineTourItemSaverInterface
     */
    public function createStateMachineTourItemSaver(): StateMachineTourItemSaverInterface
    {
        return new StateMachineTourItemSaver(
            $this->getQueryContainer()
        );
    }

    /**
     * @return TourToStateMachineBridgeInterface
     */
    protected function getStateMachineFacade(): TourToStateMachineBridgeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_STATE_MACHINE);
    }

    /**
     * @return ConcreteTourHydratorInterface|StateConcreteTourHydrator
     */
    protected function createStateConcreteTourHydrator(): ConcreteTourHydratorInterface
    {
        return new StateConcreteTourHydrator();
    }

    /**
     * @return TourToGraphhopperMapper
     */
    public function createTourToGraphhopperMapper() : TourToGraphhopperMapper
    {
        return new TourToGraphhopperMapper(
            $this->createConcreteTourModel(),
            $this->createTourOrderModel(),
            $this->getConfig()
        );
    }

    /**
     * @return BillingFacadeInterface
     */
    protected function getBillingFacade(): BillingFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_BILLING);
    }

    /**
     * @return IntegraFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getIntegraFacade() : IntegraFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_INTEGRA);
    }

    /**
     * @return DiscountQueryContainerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getDiscountQueryContainer(): DiscountQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::QUERY_CONTAINER_DISCOUNT);
    }

    /**
     * @return GraphMastersFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getGraphMastersFacade(): GraphMastersFacadeInterface
    {
        return $this
            ->getProvidedDependency(TourDependencyProvider::FACADE_GRAPHMASTERS);
    }
}
