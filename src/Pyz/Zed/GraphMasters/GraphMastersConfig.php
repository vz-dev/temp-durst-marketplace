<?php

namespace Pyz\Zed\GraphMasters;

use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class GraphMastersConfig extends AbstractBundleConfig
{
    protected const DEFAULT_GRAPHMASTERS_BASE_URL = 'https://obncmw-middlewares.nunav.net/';
    protected const DEFAULT_GRAPHMASTERS_DAYS_IN_ADVANCE = 5;

    protected const API_ENDPOINT_URL_FORMAT = '%s%s';

    protected const TOUR_REFERENCE_NAME = 'GraphmastersTourReference';
    protected const TOUR_REFERENCE_PREFIX_PART = 'GM-TOUR';
    protected const TOUR_REFERENCE_PREFIX_FORMAT= '%s-%s-';

    protected const TOUR_VIRTUAL_STATUS_MAP = [
        GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_ORDERABLE => [
            GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_IDLE,
        ],
        GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_PLANABLE => [
            GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_CLOSED,
            GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_DOWNLOADED,
        ],
        GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_IN_DELIVERY => [
            GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_RUNNING,
            GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_IN_SERVICE,
            GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_PAUSED,
        ],
        GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_DELIVERED => [
            GraphMastersConstants::GRAPHMASTERS_TOUR_STATUS_FINISHED,
        ],
        GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_EMPTY => [],
    ];

    /**
     * @return string
     */
    public function getGraphMastersBaseUrl(): string
    {
        return $this
            ->get(GraphMastersConstants::GRAPHMASTERS_BASE_URL, static::DEFAULT_GRAPHMASTERS_BASE_URL);
    }

    /**
     * @return string
     */
    public function getGraphMastersApiKey(): string
    {
        return $this
            ->get(GraphMastersConstants::GRAPHMASTERS_API_KEY);
    }

    /**
     * @return string
     */
    public function getApiEndpointImportOrder() : string
    {
        return sprintf(
            static::API_ENDPOINT_URL_FORMAT,
            $this->getGraphMastersBaseUrl(),
            GraphMastersConstants::GRAPHMASTERS_API_ENDPOINT_IMPORT_ORDER
        );
    }

    /**
     * @return string
     */
    public function getApiEndpointGetTours() : string
    {
        return sprintf(
            static::API_ENDPOINT_URL_FORMAT,
            $this->getGraphMastersBaseUrl(),
            GraphMastersConstants::GRAPHMASTERS_API_ENDPOINT_GET_TOURS
        );
    }

    /**
     * @return string
     */
    public function getApiEndpointFixTours() : string
    {
        return sprintf(
            static::API_ENDPOINT_URL_FORMAT,
            $this->getGraphMastersBaseUrl(),
            GraphMastersConstants::GRAPHMASTERS_API_ENDPOINT_FIX_TOURS
        );
    }

    /**
     * @return string
     */
    public function getApiEndpointEvaluateTimeSlots() : string
    {
        return sprintf(
            static::API_ENDPOINT_URL_FORMAT,
            $this->getGraphMastersBaseUrl(),
            GraphMastersConstants::GRAPHMASTERS_API_ENDPOINT_EVALUATE_TIME_SLOTS
        );
    }

    /**
     * @return string
     */
    public function getProjectTimeZone(): string
    {
        return $this
            ->get(ApplicationConstants::PROJECT_TIMEZONE);
    }

    /**
     * @return string
     */
    public function getTimeSlotCreationLimit() : string
    {
        return '+14days';
    }

    /**
     * @return string
     */
    public function getIntervalFormatAddMinutes() : string
    {
        return 'PT%sM';
    }

    /**
     * @return int
     */
    public function getDaysInAdvance() : int
    {
        return $this
            ->get(GraphMastersConstants::GRAPHMASTERS_DAYS_IN_ADVANCE, static::DEFAULT_GRAPHMASTERS_DAYS_IN_ADVANCE);
    }

    /**
     * @return SequenceNumberSettingsTransfer
     */
    public function getTourReferenceDefaults(): SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();
        $sequenceNumberSettingsTransfer->setName(self::TOUR_REFERENCE_NAME);

        $sequenceNumberSettingsTransfer->setPrefix('DGT-');

        return $sequenceNumberSettingsTransfer;
    }

    /**
     * @return string
     */
    public function getGraphmastersTourFilteringEarliestAllowedDate(): string
    {
        return $this->get(GraphMastersConstants::GRAPHMASTERS_TOUR_FILTERING_EARLIEST_ALLOWED_DATE);
    }

    /**
     * @return array
     */
    public function getGraphmastersTourVirtualStatusMap(): array
    {
        $tourStatusMap = self::TOUR_VIRTUAL_STATUS_MAP;

        $tourStatusMap[GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_PLANABLE_TO_IN_DELIVERY] =
            array_merge(
                static::TOUR_VIRTUAL_STATUS_MAP[GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_PLANABLE],
                static::TOUR_VIRTUAL_STATUS_MAP[GraphMastersConstants::GRAPHMASTERS_TOUR_VIRTUAL_STATUS_IN_DELIVERY]
            );

        return $tourStatusMap;
    }
}
