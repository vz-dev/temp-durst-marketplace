<?php

namespace Pyz\Zed\GraphMasters\Business\Handler;

use Generated\Shared\Transfer\GraphMastersApiActionTransfer;
use Generated\Shared\Transfer\GraphMastersApiGeoLocationTransfer;
use Generated\Shared\Transfer\GraphMastersApiGetToursResponseTransfer;
use Generated\Shared\Transfer\GraphMastersApiToursRequestTransfer;
use Generated\Shared\Transfer\GraphMastersApiTourTransfer;
use Generated\Shared\Transfer\HttpRequestErrorTransfer;
use Generated\Shared\Transfer\HttpRequestOptionsTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Orm\Zed\HttpRequest\Persistence\Map\PyzHttpRequestTableMap;
use Pyz\Service\HttpRequest\HttpRequestServiceInterface;
use Pyz\Zed\GraphMasters\Business\Exception\BadResponseException;
use Pyz\Zed\GraphMasters\Business\Exception\FailedRequestException;
use Pyz\Zed\GraphMasters\Business\Handler\Json\Request\ToursKeyRequestInterface as Request;
use Pyz\Zed\GraphMasters\Business\Handler\Json\Response\GetToursKeyResponseInterface as Response;
use Pyz\Zed\GraphMasters\Business\Model\GraphMastersSettingsInterface;
use Pyz\Zed\GraphMasters\GraphMastersConfig;
use Pyz\Zed\HttpRequest\Business\HttpRequestFacadeInterface;
use stdClass;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TourHandler implements TourHandlerInterface
{
    /**
     * @var GraphMastersSettingsInterface
     */
    protected $settings;

    /**
     * @var HttpRequestFacadeInterface
     */
    protected $httpRequestFacade;

    /**
     * @var HttpRequestServiceInterface
     */
    protected $httpRequestService;

    /**
     * @var GraphMastersConfig
     */
    protected $config;

    /**
     * @param GraphMastersSettingsInterface $settings
     * @param HttpRequestFacadeInterface $httpRequestFacade
     * @param HttpRequestServiceInterface $httpRequestService
     * @param GraphMastersConfig $config
     */
    public function __construct(
        GraphMastersSettingsInterface $settings,
        HttpRequestFacadeInterface $httpRequestFacade,
        HttpRequestServiceInterface $httpRequestService,
        GraphMastersConfig $config
    ) {
        $this->settings = $settings;
        $this->httpRequestFacade = $httpRequestFacade;
        $this->httpRequestService = $httpRequestService;
        $this->config = $config;
    }

    /**
     * @param string $depotId
     * @param array|null $tourIds
     * @param array|null $shifts
     *
     * @return GraphMastersApiToursRequestTransfer
     */
    public function createApiToursRequestTransfer(
        string $depotId,
        array $tourIds = null,
        array $shifts = null
    ): GraphMastersApiToursRequestTransfer {
        $requestTransfer = new GraphMastersApiToursRequestTransfer();

        $requestTransfer->setDepotId($depotId);

        if ($tourIds !== null && count($tourIds) > 0) {
            $requestTransfer->setTourIds($tourIds);
        }

        if ($shifts !== null && count($shifts) > 0) {
            $requestTransfer->setShifts($shifts);
        }

        return $requestTransfer;
    }

    /**
     * @param GraphMastersApiToursRequestTransfer $toursRequestTransfer
     *
     * @return GraphMastersApiGetToursResponseTransfer
     */
    public function getTours(
        GraphMastersApiToursRequestTransfer $toursRequestTransfer
    ): GraphMastersApiGetToursResponseTransfer {
        $httpRequest = $this->createHttpRequest($toursRequestTransfer, $this->config->getApiEndpointGetTours());

        $httpResponse = $this
            ->httpRequestService
            ->sendRequest($httpRequest);

        $this
            ->httpRequestFacade
            ->createHttpRequestLogEntry($httpRequest, $httpResponse);

        $this->checkResponse($httpResponse);

        $responseTransfer = $this->createGetToursResponseTransfer($httpResponse);

        return $responseTransfer;
    }

    /**
     * @param GraphMastersApiToursRequestTransfer $toursRequestTransfer
     */
    public function fixTours(GraphMastersApiToursRequestTransfer $toursRequestTransfer): void
    {
        $httpRequest = $this->createHttpRequest($toursRequestTransfer, $this->config->getApiEndpointFixTours());

        $httpResponse = $this
            ->httpRequestService
            ->sendRequest($httpRequest);

        $this
            ->httpRequestFacade
            ->createHttpRequestLogEntry($httpRequest, $httpResponse);

        $this->checkResponse($httpResponse);
    }

    /**
     * @param GraphMastersApiToursRequestTransfer $toursRequestTransfer
     * @param string $uri
     *
     * @return HttpRequestTransfer
     */
    protected function createHttpRequest(GraphMastersApiToursRequestTransfer $toursRequestTransfer, string $uri): HttpRequestTransfer
    {
        $httpRequest = new HttpRequestTransfer();

        $httpRequest
            ->setOptions($this->createHttpRequestOptions($toursRequestTransfer))
            ->setMethod(PyzHttpRequestTableMap::COL_REQUEST_METHOD_POST)
            ->setHeaders(['Authorization' => 'api-key '.$this->config->getGraphMastersApiKey()])
            ->setUri($uri);

        return $httpRequest;
    }

    /**
     * @param GraphMastersApiToursRequestTransfer $toursRequestTransfer
     *
     * @return HttpRequestOptionsTransfer
     */
    protected function createHttpRequestOptions(
        GraphMastersApiToursRequestTransfer $toursRequestTransfer
    ): HttpRequestOptionsTransfer {
        $httpRequestOptions = new HttpRequestOptionsTransfer();

        $requestBody = [];
        $requestBody[Request::DEPOT_ID] = $toursRequestTransfer->getDepotId();
        $requestBody[Request::TOUR_IDS] = $toursRequestTransfer->getTourIds();
        $requestBody[Request::SHIFTS] = $toursRequestTransfer->getShifts();

        $httpRequestOptions
            ->setJson($requestBody)
            ->setHeaders([
                'Content-Type' => 'application/json'
            ]);

        return $httpRequestOptions;
    }

    /**
     * @param HttpResponseTransfer $httpResponse
     *
     * @return GraphMastersApiGetToursResponseTransfer
     */
    protected function createGetToursResponseTransfer(
        HttpResponseTransfer $httpResponse
    ): GraphMastersApiGetToursResponseTransfer {
        $responseData = json_decode($httpResponse->getBody());

        $responseTransfer = new GraphMastersApiGetToursResponseTransfer();

        foreach ($responseData->{Response::TOURS} as $tourData) {
            $responseTransfer->addTours($this->createTourTransfer($tourData));
        }

        $responseTransfer->setUnassignedOrderIds($responseData->{Response::UNASSIGNED_ORDER_IDS});

        return $responseTransfer;
    }

    /**
     * @param stdClass $tourData
     *
     * @return GraphMastersApiTourTransfer
     */
    protected function createTourTransfer(stdClass $tourData): GraphMastersApiTourTransfer
    {
        $tourTransfer = (new GraphMastersApiTourTransfer())
            ->setId($tourData->{Response::TOUR_ID} ?? null)
            ->setShiftStart($tourData->{Response::TOUR_SHIFT_START} ?? null)
            ->setName($tourData->{Response::TOUR_NAME} ?? null)
            ->setVehicleId($tourData->{Response::TOUR_VEHICLE_ID} ?? null)
            ->setDriverId($tourData->{Response::TOUR_DRIVER_ID} ?? null);

        if (is_object($tourData->{Response::TOUR_START_LOCATION})) {
            $tourTransfer->setStartLocation(
                $this->createGeoLocationTransfer($tourData->{Response::TOUR_START_LOCATION})
            );
        }

        if (is_object($tourData->{Response::TOUR_DESTINATION_LOCATION} !== null)) {
            $tourTransfer->setDestinationLocation(
                $this->createGeoLocationTransfer($tourData->{Response::TOUR_DESTINATION_LOCATION})
            );
        }

        $tourTransfer
            ->setTourStartEta($tourData->{Response::TOUR_START_ETA} ?? null)
            ->setTourDestinationEta($tourData->{Response::TOUR_DESTINATION_ETA} ?? null)
            ->setTourStatus($tourData->{Response::TOUR_STATUS} ?? null)
            ->setVehicleStatus($tourData->{Response::TOUR_VEHICLE_STATUS} ?? null)
            ->setTotalDistanceMeters($tourData->{Response::TOUR_TOTAL_DISTANCE_METERS} ?? null)
            ->setTotalTimeSeconds($tourData->{Response::TOUR_TOTAL_TIME_SECONDS} ?? null);

        $openActionsData = $tourData->{Response::TOUR_OPEN_ACTIONS} ?? [];

        if (is_array($openActionsData)) {
            foreach ($openActionsData as $openActionData) {
                $tourTransfer->addOpenActions($this->createActionTransfer($openActionData));
            }
        }

        $finishedActionsData = $tourData->{Response::TOUR_FINISHED_ACTIONS} ?? [];

        if (is_array($finishedActionsData)) {
            foreach ($finishedActionsData as $finishedActionData) {
                $tourTransfer->addFinishedActions($this->createActionTransfer($finishedActionData));
            }
        }

        $tourTransfer
            ->setSuspendedOrderIds($tourData->{Response::TOUR_SUSPENDED_ORDER_IDS} ?? [])
            ->setUnperformedOrderIds($tourData->{Response::TOUR_UNPERFORMED_ORDER_IDS} ?? []);

        return $tourTransfer;
    }

    /**
     * @param stdClass $geoLocationData
     *
     * @return GraphMastersApiGeoLocationTransfer
     */
    protected function createGeoLocationTransfer(stdClass $geoLocationData): GraphMastersApiGeoLocationTransfer
    {
        $geoLocationTransfer = (new GraphMastersApiGeoLocationTransfer())
            ->setLat($geoLocationData->{Response::GEO_LOCATION_LAT} ?? null)
            ->setLng($geoLocationData->{Response::GEO_LOCATION_LNG} ?? null);

        return $geoLocationTransfer;
    }

    /**
     * @param stdClass $actionData
     *
     * @return GraphMastersApiActionTransfer
     */
    protected function createActionTransfer(stdClass $actionData): GraphMastersApiActionTransfer
    {
        $actionTransfer = (new GraphMastersApiActionTransfer())
            ->setActionType($actionData->{Response::ACTION_TYPE} ?? null)
            ->setStartTime($actionData->{Response::ACTION_START_TIME} ?? null)
            ->setOrderIds($actionData->{Response::ACTION_ORDER_IDS} ?? []);

        if (is_object($actionData->{Response::ACTION_LOCATION})) {
            $actionTransfer->setLocation($this->createGeoLocationTransfer($actionData));
        }

        $actionTransfer->setDistanceMeters($actionData->{Response::ACTION_DISTANCE_METERS} ?? null);

        return $actionTransfer;
    }

    /**
     * @param HttpResponseTransfer $httpResponse
     * @throws BadResponseException
     * @throws FailedRequestException
     */
    protected function checkResponse(HttpResponseTransfer $httpResponse): void
    {
        if (count($httpResponse->getErrors()) > 0) {
            /** @var HttpRequestErrorTransfer $errorTransfer */
            $errorTransfer = $httpResponse->getErrors()->offsetGet(0);

            throw FailedRequestException::build($errorTransfer->getCode(), $errorTransfer->getMessage());
        }

        if ($httpResponse->getCode() !== HttpResponse::HTTP_OK) {
            throw BadResponseException::build($httpResponse->getCodeMessage(), $httpResponse->getCode());
        }
    }
}
