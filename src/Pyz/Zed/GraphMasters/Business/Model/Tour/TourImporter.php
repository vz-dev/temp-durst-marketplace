<?php

namespace Pyz\Zed\GraphMasters\Business\Model\Tour;

use DateInterval;
use DateTime;
use Exception;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\GraphMastersApiActionTransfer;
use Generated\Shared\Transfer\GraphMastersApiTourTransfer;
use Generated\Shared\Transfer\GraphMastersOrderTransfer;
use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Generated\Shared\Transfer\GraphMastersTourTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Pyz\Zed\GraphMasters\Business\Handler\TourHandlerInterface;
use Pyz\Zed\GraphMasters\Business\Model\GraphMastersSettingsInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class TourImporter implements TourImporterInterface
{
    public const DAYS_OF_WEEK = ['sunday', 'monday', 'tuesday', 'wednesday','thursday','friday', 'saturday'];
    public const BUFFER_MINS_CUTOFF = 5;


    /**
     * @var GraphMastersSettingsInterface
     */
    protected $settingsModel;

    /**
     * @var TourHandlerInterface
     */
    protected $tourHandler;

    /**
     * @var TourInterface
     */
    protected $tourModel;

    /**
     * @var TourReferenceGeneratorInterface
     */
    protected $tourReferenceGenerator;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var TourFacadeInterface
     */
    protected $tourFacade;

    /**
     * @param GraphMastersSettingsInterface $settings
     * @param TourHandlerInterface $tourHandler
     * @param TourInterface $tourModel
     * @param TourReferenceGeneratorInterface $tourReferenceGenerator
     * @param MerchantFacadeInterface $merchantFacade
     * @param TourFacadeInterface $tourFacade
     */
    public function __construct(
        GraphMastersSettingsInterface $settings,
        TourHandlerInterface $tourHandler,
        TourInterface $tourModel,
        TourReferenceGeneratorInterface $tourReferenceGenerator,
        MerchantFacadeInterface $merchantFacade,
        TourFacadeInterface $tourFacade
    ){
        $this->settingsModel = $settings;
        $this->tourHandler = $tourHandler;
        $this->tourModel = $tourModel;
        $this->tourReferenceGenerator = $tourReferenceGenerator;
        $this->merchantFacade = $merchantFacade;
        $this->tourFacade = $tourFacade;
    }

    /**
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function importTours(): void
    {
        $activeSettings = $this->settingsModel->getActiveSettings();

        foreach ($activeSettings as $settings) {
            $fkBranch = $settings->getFkBranch();

            $currentIdleTourTransfers = $this
                ->tourModel
                ->getTodaysIdleToursByFkBranch($fkBranch);

            $requestTransfer = $this
                ->tourHandler
                ->createApiToursRequestTransfer($settings->getDepotApiId());

            $responseTransfer = $this
                ->tourHandler
                ->getTours($requestTransfer);

            $newTourTransfers = [];

            foreach ($responseTransfer->getTours() as $apiTourTransfer) {
                $tourTransfer = $this->createTourTransfer($apiTourTransfer, $settings);
                $tourTransfer = $this->tourModel->save($tourTransfer);

                if ($tourTransfer->getTourStatus() === GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_FINISHED &&
                    $tourTransfer->getEdiDepositExported() !== true
                ) {
                    $this->ediExportDeposit($tourTransfer);
                }

                $newTourTransfers[] = $tourTransfer;
            }

            $this->deleteRemovedIdleTours($currentIdleTourTransfers, $newTourTransfers);
        }
    }

    /**
     * @param GraphMastersApiTourTransfer $apiTourTransfer
     * @param GraphMastersSettingsTransfer $settings
     *
     * @return GraphMastersTourTransfer
     */
    protected function createTourTransfer(
        GraphMastersApiTourTransfer $apiTourTransfer,
        GraphMastersSettingsTransfer $settings
    ): GraphMastersTourTransfer {
        $existingTourTransfer = $this->tourModel->getTourByOriginalId($apiTourTransfer->getId());
        $fkBranch = $settings->getFkBranch();
        $deliveryOrder = 1;

        $tourTransfer = (new GraphMastersTourTransfer());

        if ($existingTourTransfer !== null) {
            $tourTransfer->setIdGraphmastersTour($existingTourTransfer->getIdGraphmastersTour());
        }

        $tourTransfer->setOriginalId($apiTourTransfer->getId() ?? null)
            ->setFkBranch($fkBranch)
            ->setReference($this->tourReferenceGenerator->generateReference())
            ->setTourCommissioningCutOff($this->getCommissioningCutOffTime($apiTourTransfer->getTourStartEta(), $settings))
            ->setTourStartEta($apiTourTransfer->getTourStartEta() ?? null)
            ->setTourDestinationEta($apiTourTransfer->getTourDestinationEta() ?? null)
            ->setTourStatus($apiTourTransfer->getTourStatus() ?? null)
            ->setVehicleStatus($apiTourTransfer->getVehicleStatus() ?? null)
            ->setTotalDistanceMeters($apiTourTransfer->getTotalDistanceMeters() ?? null)
            ->setTotalTimeSeconds($apiTourTransfer->getTotalTimeSeconds() ?? null);

        if ($apiTourTransfer->getTourStartEta() !== null) {
            $date = new DateTime($apiTourTransfer->getTourStartEta());

            $tourTransfer->setDate($date->format('Y-m-d'));
        }

        /** @var GraphMastersApiActionTransfer[] $actions */
        foreach ($apiTourTransfer->getOpenActions() as $openAction) {
            if ($openAction->getActionType() !== GraphMastersConstants::GRAPHMASTERS_ACTION_TYPE_STOP) {
                continue;
            }

            foreach ($openAction->getOrderIds() as $orderId) {
                if (is_string($orderId) &&
                    str_starts_with($orderId, GraphMastersConstants::GRAPHMASTERS_PREDICTED_ORDER_ID_PREFIX) === false
                ) {
                    $graphmastersOrderTransfer = (new GraphMastersOrderTransfer())
                        ->setFkOrderReference($orderId)
                        ->setDeliveryOrder($deliveryOrder)
                        ->setStopEta($this->fixDateTimeFormat($openAction->getStartTime()))
                        ->setStatus(GraphMastersConstants::GRAPHMASTERS_ORDER_STATUS_OPEN);

                    $tourTransfer->addGraphmastersOrders($graphmastersOrderTransfer);

                    $deliveryOrder += 1;
                }
            }
        }

        foreach ($apiTourTransfer->getFinishedActions() as $finishedAction) {
            if ($finishedAction->getActionType() !== GraphMastersConstants::GRAPHMASTERS_ACTION_TYPE_STOP) {
                continue;
            }

            foreach ($finishedAction->getOrderIds() as $orderId) {
                if (is_string($orderId) &&
                    str_starts_with($orderId, GraphMastersConstants::GRAPHMASTERS_PREDICTED_ORDER_ID_PREFIX) === false
                ) {
                    $graphmastersOrderTransfer = (new GraphMastersOrderTransfer())
                        ->setFkOrderReference($orderId)
                        ->setDeliveredAt($this->fixDateTimeFormat($finishedAction->getStartTime()))
                        ->setStatus(GraphMastersConstants::GRAPHMASTERS_ORDER_STATUS_FINISHED);

                    $tourTransfer->addGraphmastersOrders($graphmastersOrderTransfer);
                }
            }
        }

        foreach (
            array_merge(
                $apiTourTransfer->getSuspendedOrderIds(),
                $apiTourTransfer->getUnperformedOrderIds()
            ) as $orderId
        ) {
            $graphmastersOrderTransfer = (new GraphMastersOrderTransfer())->setFkOrderReference($orderId);

            $tourTransfer->addGraphmastersOrders($graphmastersOrderTransfer);
        }

        return $tourTransfer;
    }

    /**
     * @param GraphMastersTourTransfer $tourTransfer
     *
     * @throws PropelException
     */
    protected function ediExportDeposit(GraphMastersTourTransfer $tourTransfer): void
    {
        $branchTransfer = $this
            ->merchantFacade
            ->getBranchById($tourTransfer->getFkBranch());

        $ediDepositEndpointUrl = $this->getEdiDepositEndpointUrl($branchTransfer);

        $exitCode = $this
            ->tourFacade
            ->ediExportDepositById(
                $tourTransfer->getIdGraphmastersTour(),
                $ediDepositEndpointUrl,
                6000,
                true
            );

        if ($exitCode === 0) {
            $tourTransfer->setEdiDepositExported(true);

            $this->tourModel->save($tourTransfer);
        }
    }

    /**
     * @param BranchTransfer $branch
     * @return string
     */
    protected function getEdiDepositEndpointUrl(BranchTransfer $branch): string
    {
        if ($branch->getEdiExportVersion() === EdifactConstants::EDIFACT_EXPORT_VERSION_2) {
            return $branch->getEdiDepositEndpointUrl();
        } else {
            return $branch->getEdiEndpointUrl();
        }
    }

    /**
     * @param GraphMastersTourTransfer[]|array $currentIdleTourTransfers
     * @param GraphMastersTourTransfer[]|array $newTourTransfers
     *
     * @throws PropelException
     */
    protected function deleteRemovedIdleTours(array $currentIdleTourTransfers, array $newTourTransfers): void
    {
        foreach ($currentIdleTourTransfers as $currentIdleTourTransfer) {
            $idTour = $currentIdleTourTransfer->getIdGraphmastersTour();

            foreach ($newTourTransfers as $newTourTransfer) {
                if ($newTourTransfer->getIdGraphmastersTour() === $idTour) {
                    continue 2;
                }
            }

            $this->tourModel->deleteTourById($idTour);
        }
    }

    /**
     * @param string $tourStart
     * @param GraphMastersSettingsTransfer $settings
     * @return string|null
     * @throws Exception
     */
    public function getCommissioningCutOffTime(string $tourStart, GraphMastersSettingsTransfer $settings): ?string
    {
        $deliverySlot = null;
        $dayofweekNo = date('w', strtotime($tourStart));
        $dayofweek = self::DAYS_OF_WEEK[$dayofweekNo];

        $tourStartDateTime = new DateTime($tourStart);

        foreach ($settings->getOpeningTimes() as $openingTime)
        {
            $startTime = $this->splitHoursMinutes($openingTime->getStartTime());
            $startTimeDateTime = (clone $tourStartDateTime)->setTime($startTime[0], $startTime[1]);
            $endTime = $this->splitHoursMinutes($openingTime->getEndTime());
            $endTimeDateTime = (clone $tourStartDateTime)->setTime($endTime[0], $endTime[1]);

            if($openingTime->getWeekday() === $dayofweek && ($tourStartDateTime >= $startTimeDateTime && $tourStartDateTime <= $endTimeDateTime)){
                $deliverySlot = $openingTime;
                break;
            }
        }

        if($deliverySlot === null)
        {
            return null;
        }

        $deliveryStartTime = $this->splitHoursMinutes($deliverySlot->getStartTime());
        $deliveryStartDateTime = (clone $tourStartDateTime)->setTime($deliveryStartTime[0], $deliveryStartTime[1]);

        $diffTime = null;
        foreach ($settings->getCommissioningTimes() as $commissioningTime)
        {
            $endCommissioningTime = $this->splitHoursMinutes($commissioningTime->getEndTime());
            $endCommissioningTimeDateTime = (clone $tourStartDateTime)->setTime($endCommissioningTime[0], $endCommissioningTime[1]);

            if($commissioningTime->getWeekday() === $dayofweek && ($deliveryStartDateTime >= $endCommissioningTimeDateTime)){

                if($diffTime === null || $deliveryStartDateTime->getTimestamp() - $endCommissioningTimeDateTime->getTimestamp() < $diffTime)
                {
                    $commissionSlot = $commissioningTime;
                    $diffTime = $deliveryStartDateTime->getTimestamp() - $endCommissioningTimeDateTime->getTimestamp();
                }
            }
        }

        $commissionStartTime = $this->splitHoursMinutes($commissionSlot->getEndTime());
        $commissionStartDateTime = (clone $tourStartDateTime)
            ->setTime($commissionStartTime[0], $commissionStartTime[1])
            ->sub(new DateInterval($this->getCommissionInterval($settings, true)));

        return $commissionStartDateTime->format(DATE_ATOM);
    }

    /**
     * @param GraphMastersSettingsTransfer $settings
     * @param bool $inclBuffer
     * @return string
     */
    private function getCommissionInterval(GraphMastersSettingsTransfer $settings, bool $inclBuffer = false): string
    {
        $total = $settings->getLeadTime();

        if($inclBuffer === true)
        {
            $total += self::BUFFER_MINS_CUTOFF;
        }

        return sprintf('PT%sM', $total);
    }

    /**
     * @param string $timeString
     * @return array
     */
    private function splitHoursMinutes(string $timeString) : array
    {
        return explode(':', $timeString);
    }

    private function fixDateTimeFormat(string $dateTime): string
    {
        return preg_replace('/\..*(?=\+)/', '', $dateTime);
    }
}
