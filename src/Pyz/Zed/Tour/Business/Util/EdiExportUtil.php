<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-01-16
 * Time: 09:30
 */

namespace Pyz\Zed\Tour\Business\Util;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\GraphMastersTourTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTour;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Billing\Business\BillingFacadeInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Pyz\Zed\Oms\Persistence\OmsQueryContainerInterface;
use Pyz\Zed\Tour\Business\Exception\ConcreteTourNotExistsException;
use Pyz\Zed\Tour\Business\Exception\TourExportException;
use Pyz\Zed\Tour\Business\Mapper\TourExportMapper;
use Pyz\Zed\Tour\Business\Model\ConcreteTourInterface;
use Pyz\Zed\Tour\Business\Model\EdifactReferenceGeneratorInterface;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;
use Pyz\Zed\Tour\TourConfig;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class EdiExportUtil
{
    protected const DATABASE_DATETIME_FORMAT = 'Y-m-d H:i:s.u';

    protected const DATETIME_FORMAT = 'Y-m-d H:i';
    protected const GRAPHMASTERS_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var int
     */
    protected $idTour;

    /**
     * @var ConcreteTourInterface
     */
    protected $concreteTourModel;

    /**
     * @var TourQueryContainerInterface
     */
    protected $tourQueryContainer;

    /**
     * @var OmsQueryContainerInterface
     */
    protected $omsQueryContainer;

    /**
     * @var TourConfig
     */
    protected $tourConfig;

    /**
     * @var EdifactReferenceGeneratorInterface
     */
    protected $ediReferenceGenerator;

    /**
     * @var BranchTransfer
     */
    protected $branchTransfer;

    /**
     * @var BillingFacadeInterface
     */
    protected $billingFacade;

    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $graphMastersQueryContainer;

    /**
     * @var GraphMastersFacadeInterface
     */
    protected $graphMastersFacade;

    /**
     * @var bool
     */
    protected $isGraphmastersTour;

    /**
     * EdiExportUtil constructor.
     * @param int $idTour
     * @param ConcreteTourInterface $concreteTourModel
     * @param TourQueryContainerInterface $tourQueryContainer
     * @param OmsQueryContainerInterface $omsQueryContainer
     * @param TourConfig $tourConfig
     * @param EdifactReferenceGeneratorInterface $ediReferenceGenerator
     * @param BillingFacadeInterface $billingFacade
     * @param GraphMastersQueryContainerInterface $graphMastersQueryContainer
     * @param GraphMastersFacadeInterface $graphMastersFacade
     * @param bool $isGraphmastersTour
     */
    public function __construct(
        int $idTour,
        ConcreteTourInterface $concreteTourModel,
        TourQueryContainerInterface $tourQueryContainer,
        OmsQueryContainerInterface $omsQueryContainer,
        TourConfig $tourConfig,
        EdifactReferenceGeneratorInterface $ediReferenceGenerator,
        BillingFacadeInterface $billingFacade,
        GraphMastersQueryContainerInterface $graphMastersQueryContainer,
        GraphMastersFacadeInterface $graphMastersFacade,
        bool $isGraphmastersTour = false
    ) {
        $this->idTour = $idTour;
        $this->concreteTourModel = $concreteTourModel;
        $this->tourQueryContainer = $tourQueryContainer;
        $this->omsQueryContainer = $omsQueryContainer;
        $this->tourConfig = $tourConfig;
        $this->ediReferenceGenerator = $ediReferenceGenerator;
        $this->billingFacade = $billingFacade;
        $this->graphMastersQueryContainer = $graphMastersQueryContainer;
        $this->graphMastersFacade = $graphMastersFacade;
        $this->isGraphmastersTour = $isGraphmastersTour;
    }

    /**
     * @return array
     */
    protected function getStateIds(): array
    {
        $result = $this
            ->omsQueryContainer
            ->querySalesOrderItemStatesByName(
                $this
                ->tourConfig
                ->getAcceptedOmsState()
            )
            ->find();

        $states = [];

        foreach ($result as $item) {
            $states[] = $item->getIdOmsOrderItemState();
        }

        return $states;
    }

    /**
     * @return array
     */
    protected function getProcesses(): array
    {
        $activeProcesses = $this->getActiveProcesses();

        $processes = [];

        foreach ($activeProcesses as $process) {
            $processes[$process->getIdOmsOrderProcess()] = $process->getName();
        }

        asort($processes);

        return $processes;
    }

    /**
     * @return ObjectCollection
     */
    protected function getActiveProcesses(): ObjectCollection
    {
        return $this
            ->omsQueryContainer
            ->getActiveProcesses(
                $this
                ->tourConfig
                ->getActiveProcesses()
            )
            ->find();
    }

    /**
     * @return ObjectCollection
     */
    protected function getOrderItemsForConcreteTour(): ObjectCollection
    {
        return $this
            ->omsQueryContainer
            ->queryOrdersForConcreteTourInState(
                array_keys(
                    $this
                    ->getProcesses()
                ),
                $this->getStateIds(),
                $this->idTour
            )
            ->find();
    }

    /**
     * @return ObjectCollection
     */
    protected function getOrderItemsForGraphmastersTour(): ObjectCollection
    {
        return $this
            ->omsQueryContainer
            ->queryOrdersForGraphmastersTourInState(
                array_keys($this->getProcesses()),
                $this->getStateIds(),
                $this->idTour
            )
            ->find();
    }

    /**
     * @return array
     */
    protected function getFkSalesOrdersForConcreteTour(): array
    {
        $orderItems = $this
            ->getOrderItemsForConcreteTour();

        $salesOrders = [];

        /*  @var $orderItem SpySalesOrderItem */
        foreach ($orderItems as $orderItem) {
            $salesOrders[] = $orderItem->getFkSalesOrder();
        }

        $salesOrders = array_unique($salesOrders);

        return $salesOrders;
    }

    /**
     * @return array
     */
    protected function getFkSalesOrdersForGraphmastersTour(): array
    {
        $orderItems = $this->getOrderItemsForGraphmastersTour();

        $salesOrders = [];

        /** @var $orderItem SpySalesOrderItem */
        foreach ($orderItems as $orderItem) {
            $salesOrders[] = $orderItem->getFkSalesOrder();
        }

        $salesOrders = array_unique($salesOrders);

        return $salesOrders;
    }

    /**
     * @return ConcreteTourTransfer
     * @throws PropelException
     * @throws ConcreteTourNotExistsException
     */
    protected function getConcreteTour(): ConcreteTourTransfer
    {
        /*  @var $concreteTour DstConcreteTour */
        $concreteTour = $this
            ->tourQueryContainer
            ->queryConcreteTour()
            ->joinWith('DstAbstractTour dat')
            ->joinWith('SpyConcreteTimeSlot scts')
            ->joinWith('scts.SpyTimeSlot sts')
            ->joinWith('SpyBranch b')
            ->joinWith('sts.SpyDeliveryArea sda')
            ->findByIdConcreteTour($this->idTour)
            ->getFirst();

        return $this
            ->concreteTourEntityToTransfer($concreteTour);
    }

    /**
     * @return GraphMastersTourTransfer
     * @throws PropelException
     */
    protected function getGraphmastersTour(): GraphMastersTourTransfer
    {
        /** @var DstGraphmastersTour $graphmastersTour */
        $graphmastersTour = $this
            ->graphMastersQueryContainer
            ->createGraphmastersTourQuery()
            ->joinWithSpyBranch()
            ->findByIdGraphmastersTour($this->idTour)
            ->getFirst();

        return $this
            ->graphmastersTourEntityToTransfer($graphmastersTour);
    }

    /**
     * @param DstConcreteTour $concreteTour
     * @return ConcreteTourTransfer
     * @throws PropelException
     * @throws ConcreteTourNotExistsException
     */
    protected function concreteTourEntityToTransfer(DstConcreteTour $concreteTour): ConcreteTourTransfer
    {
        if ($concreteTour->getIdConcreteTour() !== null) {
            $branch = $concreteTour
                ->getSpyBranch();
            $this->branchTransfer = (new BranchTransfer())
                ->fromArray(
                    $branch
                    ->toArray(),
                    true
                );
        }

        return $this
            ->concreteTourModel
            ->getConcreteTourById($concreteTour->getIdConcreteTour());
    }

    /**
     * @param DstGraphmastersTour $graphmastersTour
     * @return GraphMastersTourTransfer
     * @throws PropelException
     */
    protected function graphmastersTourEntityToTransfer(DstGraphmastersTour $graphmastersTour): GraphMastersTourTransfer
    {
        if ($graphmastersTour->getIdGraphmastersTour() !== null) {
            $branch = $graphmastersTour->getSpyBranch();

            $this->branchTransfer = (new BranchTransfer())
                ->fromArray($branch->toArray(), true);
        }

        return $this
            ->graphMastersFacade
            ->getTourById($graphmastersTour->getIdGraphmastersTour());
    }

    /**
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return array
     * @throws TourExportException
     * @throws Exception
     */
    protected function mapData(ConcreteTourTransfer $concreteTourTransfer): array
    {
        if ($concreteTourTransfer->getIdConcreteTour() === null) {
            throw new TourExportException(
                sprintf(
                    TourExportException::ERROR_CONCRETE_TOUR_NOT_FOUND,
                    $this->idTour
                )
            );
        }

        $currentDate = new DateTime('now');

        $timeLoading = new DateInterval(
            sprintf(
                TourConfig::TIME_INTERVAL_TEMPLATE,
                0
            )
        );

        $timeApproach = new DateInterval(
            sprintf(
                TourConfig::TIME_INTERVAL_TEMPLATE,
                0
            )
        );

        $deliveryStartTime = DateTime::createFromFormat(static::DATETIME_FORMAT, sprintf(
            '%s %s',
            $concreteTourTransfer->getDate(),
            $concreteTourTransfer->getAbstractTour()->getStartTime()
        ));

        $deliveryStartTime
            ->sub($timeLoading)
            ->sub($timeApproach);

        $result = [
            TourExportMapper::PAYLOAD_CREATE_DATE => $currentDate->format(TourConfig::EDI_EDIFACT_DATE_FORMAT),
            TourExportMapper::PAYLOAD_CREATE_TIME => $currentDate->format(TourConfig::EDI_EDIFACT_TIME_FORMAT),
            TourExportMapper::PAYLOAD_CREATE_DATETIME => $currentDate->format(TourConfig::EDI_EDIFACT_DATETIME_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_DATE => $deliveryStartTime->format(TourConfig::EDI_EDIFACT_DATE_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_TIME => $deliveryStartTime->format(TourConfig::EDI_EDIFACT_TIME_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_DATETIME => $deliveryStartTime->format(TourConfig::EDI_EDIFACT_DATETIME_FORMAT),
            TourExportMapper::PAYLOAD_DESCRIPTION_RETURN_ITEM => 'REASON_FOR_RETURN',
            TourExportMapper::PAYLOAD_DRIVER => 'DRIVER_HERE',
            TourExportMapper::PAYLOAD_BILLING_REFERENCE => $this->getCurrentBillingReferenceForBranchById($this->branchTransfer->getIdBranch()),
            TourExportMapper::PAYLOAD_ILN_RECIPIENT => $this->tourConfig->getDurstIlnNumber(),
            TourExportMapper::PAYLOAD_ILN_SENDER => $this->branchTransfer->getGln(),
            TourExportMapper::PAYLOAD_ILN_DELIVERY => $this->getDurstGln(),
            TourExportMapper::PAYLOAD_IS_RETURN_ITEM => true,
            TourExportMapper::PAYLOAD_TOUR_NUMBER => $concreteTourTransfer->getTourReference(),
            TourExportMapper::PAYLOAD_ACCESS_TOKEN => $this->branchTransfer->getAccessToken(),
            TourExportMapper::PAYLOAD_DATA_TRANSFER_REFERENCE => $this->ediReferenceGenerator->generateDataTransferReferenceFromTransfer($this->branchTransfer),
            TourExportMapper::PAYLOAD_MESSAGE_REFERENCE => $this->ediReferenceGenerator->generateMessageReferenceFromTransfer($this->branchTransfer),
            TourExportMapper::PAYLOAD_BASIC_AUTH_USERNAME => $this->branchTransfer->getBasicAuthUsername(),
            TourExportMapper::PAYLOAD_BASIC_AUTH_PASSWORD => $this->branchTransfer->getBasicAuthPassword()
        ];

        return $result;
    }

    /**
     * @param GraphmastersTourTransfer $graphmastersTourTransfer
     * @return array
     * @throws TourExportException
     * @throws Exception
     */
    protected function mapGraphmastersData(GraphmastersTourTransfer $graphmastersTourTransfer): array
    {
        if ($graphmastersTourTransfer->getIdGraphmastersTour() === null) {
            throw new TourExportException(
                sprintf(TourExportException::ERROR_GRAPHMASTERS_TOUR_NOT_FOUND, $this->idTour)
            );
        }

        $currentDate = new DateTime('now');

        $projectTimeZone = new DateTimeZone($this->tourConfig->getProjectTimeZone());

        $deliveryStartTime = DateTime::createFromFormat(
            static::GRAPHMASTERS_DATETIME_FORMAT,
            sprintf('%s %s',
                $graphmastersTourTransfer->getDate(),
                $graphmastersTourTransfer->getTourStartEta()
            )
        )->setTimezone($projectTimeZone);

        $result = [
            TourExportMapper::PAYLOAD_CREATE_DATE => $currentDate->format(TourConfig::EDI_EDIFACT_DATE_FORMAT),
            TourExportMapper::PAYLOAD_CREATE_TIME => $currentDate->format(TourConfig::EDI_EDIFACT_TIME_FORMAT),
            TourExportMapper::PAYLOAD_CREATE_DATETIME => $currentDate->format(TourConfig::EDI_EDIFACT_DATETIME_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_DATE => $deliveryStartTime->format(TourConfig::EDI_EDIFACT_DATE_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_TIME => $deliveryStartTime->format(TourConfig::EDI_EDIFACT_TIME_FORMAT),
            TourExportMapper::PAYLOAD_DELIVERY_DATETIME => $deliveryStartTime->format(TourConfig::EDI_EDIFACT_DATETIME_FORMAT),
            TourExportMapper::PAYLOAD_DESCRIPTION_RETURN_ITEM => 'REASON_FOR_RETURN',
            TourExportMapper::PAYLOAD_DRIVER => 'DRIVER_HERE',
            TourExportMapper::PAYLOAD_BILLING_REFERENCE => $this->getCurrentBillingReferenceForBranchById($this->branchTransfer->getIdBranch()),
            TourExportMapper::PAYLOAD_ILN_RECIPIENT => $this->tourConfig->getDurstIlnNumber(),
            TourExportMapper::PAYLOAD_ILN_SENDER => $this->branchTransfer->getGln(),
            TourExportMapper::PAYLOAD_ILN_DELIVERY => $this->getDurstGln(),
            TourExportMapper::PAYLOAD_IS_RETURN_ITEM => true,
            TourExportMapper::PAYLOAD_TOUR_NUMBER => $graphmastersTourTransfer->getReference(),
            TourExportMapper::PAYLOAD_ACCESS_TOKEN => $this->branchTransfer->getAccessToken(),
            TourExportMapper::PAYLOAD_DATA_TRANSFER_REFERENCE => $this->ediReferenceGenerator->generateDataTransferReferenceFromTransfer($this->branchTransfer),
            TourExportMapper::PAYLOAD_MESSAGE_REFERENCE => $this->ediReferenceGenerator->generateMessageReferenceFromTransfer($this->branchTransfer),
            TourExportMapper::PAYLOAD_BASIC_AUTH_USERNAME => $this->branchTransfer->getBasicAuthUsername(),
            TourExportMapper::PAYLOAD_BASIC_AUTH_PASSWORD => $this->branchTransfer->getBasicAuthPassword()
        ];

        return $result;
    }

    /**
     * @return string
     */
    protected function getDurstGln(): string
    {
        if ($this->branchTransfer->getDurstGln() !== null) {
            return $this
                ->branchTransfer
                ->getDurstGln();
        }

        return $this
            ->tourConfig
            ->getDurstIlnNumber();
    }

    /**
     * @return array
     * @throws TourExportException
     * @throws PropelException
     * @throws ConcreteTourNotExistsException
     */
    public function getConcreteTourDataForExport(): array
    {
        $concreteTour = $this
            ->getConcreteTour();

        return $this
            ->mapData($concreteTour);
    }

    /**
     * @return array
     * @throws TourExportException
     * @throws PropelException
     */
    public function getGraphmastersTourDataForExport(): array
    {
        $graphmastersTour = $this->getGraphmastersTour();

        return $this->mapGraphmastersData($graphmastersTour);
    }

    /**
     * @param int $idBranch
     * @return string
     * @throws AmbiguousComparisonException
     */
    protected function getCurrentBillingReferenceForBranchById(int $idBranch) : string
    {
        $currentBillingPeriod = $this
            ->billingFacade
            ->getCurrentBillingPeriodForBranchById($idBranch);

        if($currentBillingPeriod !== null){
            return $currentBillingPeriod->getBillingReference();
        }

        return '';
    }

    /**
     * @param array $orders
     * @return array
     */
    protected function getOrderDataFromSalesOrders(array $orders): array
    {
        $orderArray = [];

        /* @var $order SpySalesOrder */
        foreach ($orders as $order) {
            $orderArray[$order->getIdSalesOrder()] = [
                TourExportMapper::PAYLOAD_ORDER_ID => $order->getIdSalesOrder(),
                TourExportMapper::PAYLOAD_ORDER_REFERENCE => $order->getOrderReference(),
                TourExportMapper::PAYLOAD_ORDER_DURST_CUSTOMER_REFERENCE => $order->getDurstCustomerReference() ?? null,
            ];
        }

        ksort($orderArray);

        return $orderArray;
    }
}
