<?php

namespace Pyz\Zed\Tour\Business\Stream;

use ArrayObject;
use DateTimeZone;
use Exception;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTour;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainer;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Product\Persistence\ProductQueryContainer;
use Pyz\Zed\Tour\Business\Mapper\TourExportMapper;
use Pyz\Zed\Tour\Business\Model\EdifactReferenceGenerator;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Pyz\Zed\Tour\TourConfig;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * Class GraphMastersTourExportInputStream
 * @package Pyz\Zed\GraphMasters\Business\Stream
 */
class GraphMastersTourExportInputStream extends TourExportInputStream
{
    /**
     * @var GraphMastersQueryContainer
     */
    protected $graphMastersQueryContainer;

    /**
     * @param int $idTour
     * @param OmsQueryContainerInterface $omsQueryContainer
     * @param TourConfig $tourConfig
     * @param TourQueryContainerInterface $tourQueryContainer
     * @param ProductQueryContainer $productQueryContainer
     * @param EdifactReferenceGenerator $edifactReferenceGenerator
     * @param BillingFacadeInterface $billingFacade
     * @param GraphMastersQueryContainer $graphMastersQueryContainer
     * @param EdifactFacadeInterface $edifactFacade
     * @throws Exception
     */
    public function __construct(
        int $idTour,
        OmsQueryContainerInterface $omsQueryContainer,
        TourConfig $tourConfig,
        TourQueryContainerInterface $tourQueryContainer,
        ProductQueryContainer $productQueryContainer,
        EdifactReferenceGenerator $edifactReferenceGenerator,
        BillingFacadeInterface $billingFacade,
        GraphMastersQueryContainer $graphMastersQueryContainer,
        EdifactFacadeInterface $edifactFacade
    ) {
        parent::__construct(
            $idTour,
            $omsQueryContainer,
            $tourConfig,
            $tourQueryContainer,
            $productQueryContainer,
            $edifactReferenceGenerator,
            $billingFacade,
            $edifactFacade
        );

        $this->graphMastersQueryContainer = $graphMastersQueryContainer;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function open(): bool
    {
        /** @var DstGraphmastersTour $graphmastersTour */
        $graphmastersTour = $this
            ->graphMastersQueryContainer
            ->createGraphmastersTourQuery()
            ->joinWithSpyBranch()
            ->findByIdGraphmastersTour($this->idTour)
            ->getFirst();

        $this->exportVersion = $this->edifactFacade->getExportVersion();

        $items = $this->findTourOrderItems();

        $exportArray = [];

        if ($graphmastersTour->getIdGraphmastersTour() !== null) {
            $graphmastersTourData = $this
                ->getGraphmastersTourDataFromEntity($graphmastersTour);

            $orderItems = $this
                ->getOrderItemDataFromSalesOrderItems($items);

            if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_1) {
                $exportArray = $this
                    ->consolidateProductsBySku($orderItems, $graphmastersTourData);
            } else if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
                $orders = $this->getTourOrders($items);

                $orderArray = $this->getOrderDataFromSalesOrders($orders);

                $exportArray = $this
                    ->mergeOrdersAndItems($orderArray, $orderItems, $graphmastersTourData);
            }
        }

        $this->iterator = (new ArrayObject($exportArray))
            ->getIterator();

        return true;
    }

    /**
     * @param DstGraphmastersTour $graphmastersTour
     * @return array
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    protected function getGraphmastersTourDataFromEntity(DstGraphmastersTour $graphmastersTour): array
    {
        $branch = $graphmastersTour->getSpyBranch();

        $projectTimezone = new DateTimeZone($this->tourConfig->getProjectTimeZone());

        $deliveryStartDateTime = $graphmastersTour
            ->getTourStartEta()
            ->setTimezone($projectTimezone);

        return [
            TourExportMapper::PAYLOAD_CREATE_DATE => $this->currentDate->format(TourConfig::EDI_EDIFACT_DATE_FORMAT),
            TourExportMapper::PAYLOAD_CREATE_TIME => $this->currentDate->format(TourConfig::EDI_EDIFACT_TIME_FORMAT),
            TourExportMapper::PAYLOAD_CREATE_DATETIME => $this->currentDate->format(TourConfig::EDI_EDIFACT_DATETIME_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_DATE => $deliveryStartDateTime->format(TourConfig::EDI_EDIFACT_DATE_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_TIME => $deliveryStartDateTime->format(TourConfig::EDI_EDIFACT_TIME_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_DATETIME => $deliveryStartDateTime->format(TourConfig::EDI_EDIFACT_DATETIME_FORMAT),
            TourExportMapper::PAYLOAD_DESCRIPTION_RETURN_ITEM => 'REASON_FOR_RETURN',
            TourExportMapper::PAYLOAD_DRIVER => 'DRIVER_HERE',
            TourExportMapper::PAYLOAD_BILLING_REFERENCE => $this->getCurrentBillingReferenceForBranchById($branch->getIdBranch()),
            TourExportMapper::PAYLOAD_ILN_RECIPIENT => $this->tourConfig->getDurstIlnNumber(),
            TourExportMapper::PAYLOAD_ILN_SENDER => $branch->getGln(),
            TourExportMapper::PAYLOAD_ILN_DELIVERY => $this->getDurstGln($branch),
            TourExportMapper::PAYLOAD_IS_RETURN_ITEM => false,
            TourExportMapper::PAYLOAD_TOUR_NUMBER => $graphmastersTour->getReference(),
            TourExportMapper::PAYLOAD_ACCESS_TOKEN => $branch->getAccessToken(),
            TourExportMapper::PAYLOAD_DATA_TRANSFER_REFERENCE => $this->sequenceGenerator->generateDataTransferReference($branch),
            TourExportMapper::PAYLOAD_MESSAGE_REFERENCE => $this->sequenceGenerator->generateMessageReference($branch),
            TourExportMapper::PAYLOAD_BASIC_AUTH_USERNAME => $branch->getBasicAuthUsername(),
            TourExportMapper::PAYLOAD_BASIC_AUTH_PASSWORD => $branch->getBasicAuthPassword()
        ];
    }

    /**
     * @return ObjectCollection
     */
    protected function findTourOrderItems(): ObjectCollection
    {
        $stateIds = $this->getStateIds();
        $processes = $this->getProcesses();

        $itemQuery = $this
            ->omsQueryContainer
            ->queryOrdersForGraphmastersTourInState(
                array_keys($processes),
                $stateIds,
                $this->idTour
            );

        if ($this->exportVersion === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            $itemQuery->joinWithOrder();
        }

        return $itemQuery->find();
    }
}
