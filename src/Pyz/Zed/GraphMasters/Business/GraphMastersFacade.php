<?php

namespace Pyz\Zed\GraphMasters\Business;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\DriverAppTourTransfer;
use Generated\Shared\Transfer\DriverTransfer;
use Generated\Shared\Transfer\GraphMastersDeliveryAreaCategoryTransfer;
use Generated\Shared\Transfer\GraphMastersOrderTransfer;
use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Generated\Shared\Transfer\GraphMastersTourTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTour;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\Exception\EntityNotFoundException;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Spryker\Zed\Sales\Business\Exception\InvalidSalesOrderException;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

/**
 * @method GraphMastersBusinessFactory getFactory()
 */
class GraphMastersFacade extends AbstractFacade implements GraphMastersFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param int $idSalesOrder
     * @return void
     */
    public function importOrder(QuoteTransfer $quoteTransfer, int $idSalesOrder): void
    {
        $this
            ->getFactory()
            ->createOrderImporter()
            ->importOrder($quoteTransfer, $idSalesOrder);
    }

    /**
     * {@inheritDoc}
     *
     * @param GraphMastersSettingsTransfer $transfer
     *
     * @return void
     */
    public function saveSetting(GraphMastersSettingsTransfer $transfer): void
    {
        $this
            ->getFactory()
            ->createGraphMastersSettingsModel()
            ->save($transfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSettings
     *
     * @return void
     */
    public function removeSettings(int $idSettings): void
    {
        $this
            ->getFactory()
            ->createGraphMastersSettingsModel()
            ->remove($idSettings);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return bool
     */
    public function doesBranchUseGraphmasters(int $idBranch): bool
    {
        return $this
            ->getFactory()
            ->createGraphMastersSettingsModel()
            ->doesBranchUseGraphmasters($idBranch);
    }

    /**
     * @param int $idSettings
     * @param bool $withRelatedObjects
     * @return GraphMastersSettingsTransfer
     * @throws PropelException
     */
    public function getSettingsById(int $idSettings, bool $withRelatedObjects = false): GraphMastersSettingsTransfer
    {
        return $this
            ->getFactory()
            ->createGraphMastersSettingsModel()
            ->getSettingsById($idSettings, $withRelatedObjects);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return GraphMastersSettingsTransfer
     */
    public function getSettingsByIdBranch(int $idBranch): GraphMastersSettingsTransfer
    {
        return $this
            ->getFactory()
            ->createGraphMastersSettingsModel()
            ->getSettingsByIdBranch(
                $idBranch
            );
    }

    public function getDeliveryAreaCategoryById(int $idCategory, bool $currentBranchOnly = false): GraphMastersDeliveryAreaCategoryTransfer
    {
        return $this
            ->getFactory()
            ->createCategoryModel()
            ->getDeliveryAreaCategoryById($idCategory, $currentBranchOnly);
    }

    /**
     * @param GraphMastersDeliveryAreaCategoryTransfer $deliveryAreaCategoryTransfer
     * @param int|null $fkBranch
     * @return GraphMastersDeliveryAreaCategoryTransfer
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function saveDeliveryAreaCategory(
        GraphMastersDeliveryAreaCategoryTransfer $deliveryAreaCategoryTransfer,
        int $fkBranch = null
    ): GraphMastersDeliveryAreaCategoryTransfer {
        return $this
            ->getFactory()
            ->createCategoryModel()
            ->saveDeliveryAreaCategory($deliveryAreaCategoryTransfer, $fkBranch);
    }

    /**
     * @param int $catId
     * @return mixed
     */
    public function getCategoryDeliveryAreasByCategoryId(int $catId)
    {
        return $this
            ->getFactory()
            ->createCategoryModel()
            ->getDeliveryAreasByCategoryId($catId);
    }

    public function evaluateTimeSlot(AppApiRequestTransfer $appApiRequestTransfer) : AppApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createTimeSlotHandler()
            ->evaluateTimeSlot($appApiRequestTransfer);
    }

    /**
     * @return void
     */
    public function generateTimeSlots() : void
    {
        $this
            ->getFactory()
            ->createTimeSlotGenerator()
            ->createTimeSlotsForTimeSlotUntilLimit();
    }

    /**
     * @param int $idBranch
     * @return GraphMastersDeliveryAreaCategoryTransfer[]
     * @throws AmbiguousComparisonException
     */
    public function getDeliveryAreaCategoriesByIdBranch(int $idBranch): array
    {
        return $this
            ->getFactory()
            ->createCategoryModel()
            ->getDeliveryAreaCategoriesByIdBranch($idBranch);
    }

    /**
     * @param int $idDeliveryAreaCategory
     * @throws PropelException
     */
    public function removeDeliveryAreaCategory(int $idDeliveryAreaCategory): void
    {
        $this
            ->getFactory()
            ->createCategoryModel()
            ->removeDeliveryAreaCategory($idDeliveryAreaCategory);
    }

    /**
     * {@inheritdoc}
     *
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $timeslotData
     * @param LocaleTransfer $locale
     * @return PageMapTransfer
     */
    public function buildTimeslotPageMap(PageMapBuilderInterface $pageMapBuilder, array $timeslotData, LocaleTransfer $locale): PageMapTransfer
    {
        return $this
            ->getFactory()
            ->createGMTimeslotDataPageMapBuilder()
            ->buildPageMap($pageMapBuilder, $timeslotData, $locale);
    }


    /**
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function importTours(): void
    {
        $this
            ->getFactory()
            ->createTourImporter()
            ->importTours();
    }

    /**
     * @param int $idTour
     * @return GraphMastersTourTransfer
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function getTourById(int $idTour): GraphMastersTourTransfer
    {
        return $this
            ->getFactory()
            ->createTourModel()
            ->getTourById($idTour);
    }

    /**
     * {@inheritDoc}
     *
     * @param DstGraphmastersTour $tourEntity
     * @return GraphMastersTourTransfer
     */
    public function convertTourEntityToTransfer(DstGraphmastersTour $tourEntity): GraphMastersTourTransfer
    {
        return $this
            ->getFactory()
            ->createTourModel()
            ->entityToTransfer($tourEntity);
    }

    /**
     * @param int $idTour
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function fixTourById(int $idTour): void
    {
        $this
            ->getFactory()
            ->createTourModel()
            ->fixTourById($idTour);
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function fixOpenToursCutoffReached() : void
    {
        $this
            ->getFactory()
            ->createTourModel()
            ->fixOpenToursCutoffReached();
    }

    /**
     * {@inheritdoc}
     *
     * @param GraphMastersTourTransfer $graphmastersTourTransfer
     */
    public function commentTour(GraphMastersTourTransfer $graphmastersTourTransfer): void
    {
        $this
            ->getFactory()
            ->createTourModel()
            ->comment($graphmastersTourTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param DriverTransfer $driverTransfer
     * @return array|DriverAppTourTransfer[]
     * @throws ContainerKeyNotFoundException
     */
    public function getToursForDriver(DriverTransfer $driverTransfer): array
    {
        return $this
            ->getFactory()
            ->createTourModel()
            ->getToursForDriver($driverTransfer);
    }

    /**
     * @param string $orderReference
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function markOrderFinishedByReference(string $orderReference): void
    {
        $this
            ->getFactory()
            ->createGraphmastersOrderModel()
            ->markOrderFinishedByReference($orderReference);
    }

    /**
     * @param string $orderReference
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function markOrderCancelledByReference(string $orderReference): void
    {
        $this
            ->getFactory()
            ->createGraphmastersOrderModel()
            ->markOrderCancelledByReference($orderReference);
    }

    /**
     * {@inheritDoc}
     *
     * @param GraphMastersOrderTransfer $transfer
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function saveGraphmastersOrder(GraphMastersOrderTransfer $transfer): void
    {
        $this
            ->getFactory()
            ->createGraphmastersOrderModel()
            ->save($transfer);
    }

    /**
     * @param string $orderReference
     * @return bool
     * @throws ContainerKeyNotFoundException
     * @throws InvalidSalesOrderException
     * @throws PropelException
     */
    public function isOrderMarkedCancelled(string $orderReference): bool
    {
        return $this
            ->getFactory()
            ->createGraphmastersOrderModel()
            ->isOrderMarkedCancelled($orderReference);
    }

    /**
     * @param string $tourReference
     * @return GraphMastersTourTransfer
     * @throws ContainerKeyNotFoundException
     * @throws EntityNotFoundException
     * @throws PropelException
     */
    public function getTourByReference(string $tourReference): GraphMastersTourTransfer
    {
        return $this
            ->getFactory()
            ->createTourModel()
            ->getTourByReference($tourReference);
    }

    /**
     * @param string $orderReference
     * @return GraphMastersOrderTransfer
     * @throws EntityNotFoundException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function getOrderByReference(string $orderReference): GraphMastersOrderTransfer
    {
        return $this
            ->getFactory()
            ->createGraphmastersOrderModel()
            ->getOrderByFkOrderReference($orderReference);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $zipCode
     * @param string $branchCode
     * @return bool
     */
    public function getDeliversByZipAndBranchCode(string $zipCode, string $branchCode) : bool
    {
        return $this
            ->getFactory()
            ->createCategoryModel()
            ->getDeliversByZipAndBranchCode($zipCode, $branchCode);
    }
}
