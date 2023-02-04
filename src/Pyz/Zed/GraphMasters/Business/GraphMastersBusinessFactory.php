<?php

namespace Pyz\Zed\GraphMasters\Business;

use Pyz\Service\HttpRequest\HttpRequestServiceInterface;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Pyz\Zed\GraphMasters\Business\Generator\TimeSlotGenerator;
use Pyz\Zed\GraphMasters\Business\Generator\TimeSlotGeneratorInterface;
use Pyz\Zed\GraphMasters\Business\Handler\OrderHandler;
use Pyz\Zed\GraphMasters\Business\Handler\OrderHandlerInterface;
use Pyz\Zed\GraphMasters\Business\Handler\TimeSlotHandler;
use Pyz\Zed\GraphMasters\Business\Handler\TimeSlotHandlerInterface;
use Pyz\Zed\GraphMasters\Business\Handler\TourHandler;
use Pyz\Zed\GraphMasters\Business\Handler\TourHandlerInterface;
use Pyz\Zed\GraphMasters\Business\Map\GMTimeslotDataPageMapBuilder;
use Pyz\Zed\GraphMasters\Business\Model\Category;
use Pyz\Zed\GraphMasters\Business\Model\CategoryInterface;
use Pyz\Zed\GraphMasters\Business\Model\CommissioningTime;
use Pyz\Zed\GraphMasters\Business\Model\CommissioningTimeInterface;
use Pyz\Zed\GraphMasters\Business\Model\GraphmastersOrder\GraphmastersOrder;
use Pyz\Zed\GraphMasters\Business\Model\GraphmastersOrder\GraphmastersOrderInterface;
use Pyz\Zed\GraphMasters\Business\Model\GraphMastersSettings;
use Pyz\Zed\GraphMasters\Business\Model\GraphMastersSettingsInterface;
use Pyz\Zed\GraphMasters\Business\Model\OpeningTime;
use Pyz\Zed\GraphMasters\Business\Model\OpeningTimeInterface;
use Pyz\Zed\GraphMasters\Business\Model\Order\OrderImporter;
use Pyz\Zed\GraphMasters\Business\Model\Order\OrderImporterInterface;
use Pyz\Zed\GraphMasters\Business\Model\Tour\Tour;
use Pyz\Zed\GraphMasters\Business\Model\Tour\TourImporter;
use Pyz\Zed\GraphMasters\Business\Model\Tour\TourImporterInterface;
use Pyz\Zed\GraphMasters\Business\Model\Tour\TourInterface;
use Pyz\Zed\GraphMasters\Business\Model\Tour\TourReferenceGenerator;
use Pyz\Zed\GraphMasters\Business\Model\Tour\TourReferenceGeneratorInterface;
use Pyz\Zed\GraphMasters\GraphMastersConfig;
use Pyz\Zed\GraphMasters\GraphMastersDependencyProvider;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainer;
use Pyz\Zed\HttpRequest\Business\HttpRequestFacadeInterface;
use Pyz\Zed\Integra\Business\IntegraFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Touch\Business\TouchFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;

/**
 * @method GraphMastersConfig getConfig()
 * @method GraphMastersQueryContainer getQueryContainer()
 */
class GraphMastersBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return OrderImporterInterface
     */
    public function createOrderImporter() : OrderImporterInterface
    {
        return new OrderImporter(
            $this->createGraphMastersSettingsModel(),
            $this->getSalesFacade(),
            $this->createOrderHandler(),
            $this->createCategoryModel()
        );
    }

    /**
     * @return GraphMastersSettingsInterface
     */
    public function createGraphMastersSettingsModel() : GraphMastersSettingsInterface
    {
        return new GraphMastersSettings(
            $this->getQueryContainer(),
            $this->createOpeningTimeModel(),
            $this->createCommissioningTimeModel(),
            $this->getTouchFacade()
        );
    }

    /**
     * @return CategoryInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createCategoryModel() : CategoryInterface
    {
        $currentBranchId = $this->getMerchantFacade()->hasCurrentBranch()
            ? $this->getMerchantFacade()->getCurrentBranch()->getIdBranch()
            : null;

        return new Category(
            $this->getQueryContainer(),
            $this->getTouchFacade(),
            $currentBranchId
        );
    }

    /**
     * @return HttpRequestFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getHttpRequestFacade(): HttpRequestFacadeInterface
    {
        return $this
            ->getProvidedDependency(GraphMastersDependencyProvider::FACADE_HTTP_REQUEST);
    }

    /**
     * @return HttpRequestServiceInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getHttpRequestService(): HttpRequestServiceInterface
    {
        return $this
            ->getProvidedDependency(GraphMastersDependencyProvider::SERVICE_HTTP_REQUEST);
    }

    /**
     * @return SalesFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getSalesFacade(): SalesFacadeInterface
    {
        return $this
            ->getProvidedDependency(GraphMastersDependencyProvider::FACADE_SALES);
    }

    /**
     * @return TimeSlotHandler
     * @throws ContainerKeyNotFoundException
     */
    public function createTimeSlotHandler() : TimeSlotHandlerInterface
    {
        return new TimeSlotHandler(
            $this->createGraphMastersSettingsModel(),
            $this->getHttpRequestFacade(),
            $this->getHttpRequestService(),
            $this->getConfig()
        );
    }

    /**
     * @return TimeSlotGeneratorInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createTimeSlotGenerator() : TimeSlotGeneratorInterface
    {
        return new TimeSlotGenerator(
            $this->createCategoryModel(),
            $this->getConfig(),
            $this->getQueryContainer(),
            $this->getTouchFacade()
        );
    }

    /**
     * @return OpeningTimeInterface
     */
    public function createOpeningTimeModel(): OpeningTimeInterface
    {
        return new OpeningTime(
            $this->getQueryContainer(),
            $this->getConfig()
        );
    }

    /**
     * @return CommissioningTimeInterface
     */
    public function createCommissioningTimeModel(): CommissioningTimeInterface
    {
        return new CommissioningTime(
            $this->getQueryContainer(),
            $this->getConfig()
        );
    }

    /**
     * @return TourImporterInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createTourImporter(): TourImporterInterface
    {
        return new TourImporter(
            $this->createGraphMastersSettingsModel(),
            $this->createTourHandler(),
            $this->createTourModel(),
            $this->createTourReferenceGenerator(),
            $this->getMerchantFacade(),
            $this->getTourFacade()
        );
    }

    /**
     * @return TourHandlerInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createTourHandler(): TourHandlerInterface
    {
        return new TourHandler(
            $this->createGraphMastersSettingsModel(),
            $this->getHttpRequestFacade(),
            $this->getHttpRequestService(),
            $this->getConfig()
        );
    }

    /**
     * @return TourInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createTourModel(): TourInterface
    {
        return new Tour(
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->getSalesFacade(),
            $this->createGraphMastersSettingsModel(),
            $this->createTourHandler(),
            $this->createGraphmastersOrderModel(),
            $this->getTourFacade(),
            $this->getOmsQueryContainer(),
            $this->getIntegraFacade(),
            $this->getDiscountQueryContainer(),
            $this->getMerchantFacade()
        );
    }

    /**
     * @return TourReferenceGeneratorInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createTourReferenceGenerator(): TourReferenceGeneratorInterface
    {
        $sequenceNumberSettings = $this->getConfig()->getTourReferenceDefaults();

        return new TourReferenceGenerator(
            $this->getSequenceNumberFacade(),
            $sequenceNumberSettings
        );
    }

    /**
     * @return OrderHandlerInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createOrderHandler(): OrderHandlerInterface
    {
        return new OrderHandler(
            $this->createGraphMastersSettingsModel(),
            $this->getHttpRequestFacade(),
            $this->getHttpRequestService(),
            $this->getConfig()
        );
    }

    /**
     * @return MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this->getProvidedDependency(GraphMastersDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return TouchFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getTouchFacade(): TouchFacadeInterface
    {
        return $this->getProvidedDependency(GraphMastersDependencyProvider::FACADE_TOUCH);
    }

    /**
     * @return GMTimeslotDataPageMapBuilder
     */
    public function createGMTimeslotDataPageMapBuilder(): GMTimeslotDataPageMapBuilder
    {
        return new GMTimeslotDataPageMapBuilder();
    }

    /**
     * @return SequenceNumberFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getSequenceNumberFacade(): SequenceNumberFacadeInterface
    {
        return $this->getProvidedDependency(GraphMastersDependencyProvider::FACADE_SEQUENCE_NUMBER);
    }

    /**
     * @return GraphmastersOrderInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createGraphmastersOrderModel(): GraphmastersOrderInterface
    {
        return new GraphmastersOrder(
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->createGraphMastersSettingsModel(),
            $this->getSalesFacade(),
            $this->createOrderHandler()
        );
    }

    /**
     * @return TourFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getTourFacade(): TourFacadeInterface
    {
        return $this->getProvidedDependency(GraphMastersDependencyProvider::FACADE_TOUR);
    }

    /**
     * @return OmsQueryContainerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getOmsQueryContainer(): OmsQueryContainerInterface
    {
        return $this->getProvidedDependency(GraphMastersDependencyProvider::QUERY_CONTAINER_OMS);
    }

    /**
     * @return IntegraFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getIntegraFacade(): IntegraFacadeInterface
    {
        return $this->getProvidedDependency(GraphMastersDependencyProvider::FACADE_INTEGRA);
    }

    /**
     * @return DiscountQueryContainerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getDiscountQueryContainer(): DiscountQueryContainerInterface
    {
        return $this->getProvidedDependency(GraphMastersDependencyProvider::QUERY_CONTAINER_DISCOUNT);
    }
}
