<?php
/**
 * Durst - project - TourOrderSorter.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.19
 * Time: 10:49
 */

namespace Pyz\Zed\Graphhopper\Business\Model;


use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\GraphhopperTourTransfer;
use Generated\Shared\Transfer\HttpRequestOptionsTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\HttpRequest\Persistence\Map\PyzHttpRequestTableMap;
use Pyz\Service\HttpRequest\HttpRequestServiceInterface;
use Pyz\Shared\Graphhopper\GraphhopperConstants;
use Pyz\Zed\Graphhopper\Business\Exception\GenerateOptimizeJobFailException;
use Pyz\Zed\Graphhopper\Business\Exception\NoOrdersForConcreteTourException;
use Pyz\Zed\Graphhopper\Business\Exception\NoVehicleRouteFoundException;
use Pyz\Zed\Graphhopper\Business\Exception\OptimizeJobTerminatedException;
use Pyz\Zed\Graphhopper\Business\Exception\UnassignedDeliveryAddressException;
use Pyz\Zed\Graphhopper\Business\Handler\Json\Request\OptimizeKeyRequestInterface;
use Pyz\Zed\Graphhopper\Business\Handler\Json\Response\OptimizeKeyResponseInterface;
use Pyz\Zed\Graphhopper\Dependency\GraphhopperToHttpRequestBridgeInterface;
use Pyz\Zed\Graphhopper\GraphhopperConfig;
use stdClass;

class TourOrderSorter implements TourOrderSorterInterface
{
    protected const LOCATION_PREFIX = 'location-';

    /**
     * @var \Pyz\Service\HttpRequest\HttpRequestServiceInterface
     */
    protected $httpRequestService;

    /**
     * @var \Pyz\Zed\Graphhopper\Dependency\GraphhopperToHttpRequestBridgeInterface
     */
    protected $httpRequestBridgeFacade;

    /**
     * @var \Pyz\Zed\Graphhopper\GraphhopperConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Graphhopper\Business\Model\GeocodingInterface
     */
    protected $geoCoder;

    /**
     * @var BranchTransfer
     */
    protected $branch = null;

    /**
     * @var ConcreteTourTransfer
     */
    protected $concreteTour = null;

    /**
     * @var int
     */
    protected $totalItems = 0;

    /**
     * @var array
     */
    protected $timeslotOrderIdMap = [];

    /**
     * @var array
     */
    protected $idToStopMap = [];

    /**
     * @var GraphhopperTourTransfer
     */
    protected $grapphopperTourTransfer;

    /**
     * TourOrderSorter constructor.
     * @param HttpRequestServiceInterface $httpRequestService
     * @param GraphhopperToHttpRequestBridgeInterface $httpRequestBridgeFacade
     * @param GraphhopperConfig $config
     */
    public function __construct(
        HttpRequestServiceInterface $httpRequestService,
        GraphhopperToHttpRequestBridgeInterface $httpRequestBridgeFacade,
        GraphhopperConfig $config
    )
    {
        $this->httpRequestService = $httpRequestService;
        $this->httpRequestBridgeFacade = $httpRequestBridgeFacade;
        $this->config = $config;
    }

    /**
     * @param GraphhopperTourTransfer $graphhopperTourTransfer
     * @return GraphhopperTourTransfer
     * @throws GenerateOptimizeJobFailException
     * @throws NoOrdersForConcreteTourException
     * @throws NoVehicleRouteFoundException
     * @throws OptimizeJobTerminatedException
     * @throws UnassignedDeliveryAddressException
     */
    public function orderTourOrders(GraphhopperTourTransfer $graphhopperTourTransfer): GraphhopperTourTransfer
    {
        $graphhopperTourTransfer
            ->requireEndLocation()
            ->requireStartLocation()
            ->requireStops();

        $this->grapphopperTourTransfer = $graphhopperTourTransfer;

        $orderAddresses = $this
            ->getTourOrderAddresses($graphhopperTourTransfer->getStops());

        $httpRequestTransfer = $this
            ->createHttpRequestTransfer(
                $orderAddresses
            );

        $httpResponseTransfer = $this
            ->httpRequestService
            ->sendRequest(
                $httpRequestTransfer
            );

        $this
            ->httpRequestBridgeFacade
            ->createHttpRequestLogEntry(
                $httpRequestTransfer,
                $httpResponseTransfer
            );

        if ($httpResponseTransfer->getErrors()->count() > 0) {
            throw new GenerateOptimizeJobFailException(
                GenerateOptimizeJobFailException::MESSAGE
            );
        }

        $orderResponse = $this
            ->callJobServicePeriodical(
                $httpResponseTransfer
            );

        $sortedStops = $this
            ->sortStopsByAddresses($orderResponse);

        $this
          ->updateSortingInStops($sortedStops);

        return $this->grapphopperTourTransfer;
    }

    /**
     * @param \ArrayObject $stops
     * @return array
     */
    protected function getTourOrderAddresses(\ArrayObject $stops): array
    {
        $stopAddresses = [];

        foreach ($stops as $stop) {

            if(array_key_exists($stop->getTimeslotId(), $this->timeslotOrderIdMap) !== true)
            {
                $this->timeslotOrderIdMap[$stop->getTimeslotId()] = [];
            }
            $this->timeslotOrderIdMap[$stop->getTimeslotId()][] = $stop->getId();
            $this->idToStopMap[$stop->getId()] = $stop;

            $stopAddresses[] = [
                OptimizeKeyRequestInterface::SERVICES_ID => $stop->getId(),
                OptimizeKeyRequestInterface::SERVICES_NAME => $stop->getName(),
                OptimizeKeyRequestInterface::SERVICES_ADDRESS_LOCATION_ID => $stop->getLocationId(),
                OptimizeKeyRequestInterface::SERVICES_ADDRESS_LAT => $stop->getAddressLat(),
                OptimizeKeyRequestInterface::SERVICES_ADDRESS_LON => $stop->getAddressLng(),
                OptimizeKeyRequestInterface::SERVICES_ADDRESS_STREET_HINT => $stop->getName(),
                OptimizeKeyRequestInterface::SERVICES_TIME_WINDOWS_EARLIEST => $stop->getConstraintEarliest()->getTimestamp(),
                OptimizeKeyRequestInterface::SERVICES_TIME_WINDOWS_LATEST => $stop->getConstraintLatest()->getTimestamp(),
                OptimizeKeyRequestInterface::SERVICES_SIZE => $stop->getItemCount(),
            ];
        }

        return $stopAddresses;
    }

    /**
     * @param array $stopAddresses
     * @return \Generated\Shared\Transfer\HttpRequestTransfer
     */
    protected function createHttpRequestTransfer(array $stopAddresses): HttpRequestTransfer
    {
        $httpRequestTransfer = new HttpRequestTransfer();

        $options = $this
            ->createHttpRequestOptions($stopAddresses);

        $httpRequestTransfer
            ->setOptions($options)
            ->setMethod(PyzHttpRequestTableMap::COL_REQUEST_METHOD_POST)
            ->setUri(GraphhopperConstants::URL_OPTIMIZE);

        return $httpRequestTransfer;
    }

    /**
     * @param array $stopAddresses
     * @return \Generated\Shared\Transfer\HttpRequestOptionsTransfer
     */
    protected function createHttpRequestOptions(array $stopAddresses): HttpRequestOptionsTransfer
    {
        $httpRequestOptionsTransfer = new HttpRequestOptionsTransfer();

        $requestBody = [];

        $requestBody[OptimizeKeyRequestInterface::VEHICLES][] = $this
            ->createVehicleArray();
        $requestBody[OptimizeKeyRequestInterface::VEHICLE_TYPES][] = $this
            ->createVehicleTypeArray();
        $requestBody[OptimizeKeyRequestInterface::SERVICES] = [];
        foreach ($stopAddresses as $orderAddress) {
            $requestBody[OptimizeKeyRequestInterface::SERVICES][] = $this
                ->createServicesArray($orderAddress);
        }
        $requestBody[OptimizeKeyRequestInterface::OBJECTIVES] = $this
            ->createObjectivesArray();
        $requestBody[OptimizeKeyRequestInterface::CONFIGURATION] = $this
            ->createConfigurationArray();

        $httpRequestOptionsTransfer
            ->setQuery([
                OptimizeKeyRequestInterface::OPTIMIZE_API_KEY => $this->config->getGraphhopperApiKey()
            ])
            ->setJson($requestBody)
            ->setHeaders([
                'Content-Type' => 'application/json'
            ]);

        return $httpRequestOptionsTransfer;
    }

    /**
     * @return array
     */
    protected function createVehicleArray(): array
    {
        $vehicles = [
            OptimizeKeyRequestInterface::VEHICLES_VEHICLE_ID => sprintf(
                'vehicle-%d',
                $this
                    ->grapphopperTourTransfer
                    ->getStartLocation()
                    ->getVehicleId()
            ),
            OptimizeKeyRequestInterface::VEHICLES_TYPE_ID => $this->grapphopperTourTransfer->getVehicleTypeName(),
            OptimizeKeyRequestInterface::VEHICLES_START_ADDRESS => [
                OptimizeKeyRequestInterface::VEHICLES_START_ADDRESS_LOCATION_ID => $this->grapphopperTourTransfer->getStartLocation()->getLocationId(),
                OptimizeKeyRequestInterface::VEHICLES_START_ADDRESS_LAT => $this->grapphopperTourTransfer->getStartLocation()->getAddressLat(),
                OptimizeKeyRequestInterface::VEHICLES_START_ADDRESS_LON => $this->grapphopperTourTransfer->getStartLocation()->getAddressLng(),
                OptimizeKeyRequestInterface::VEHICLES_START_ADDRESS_STREET_HINT => $this->grapphopperTourTransfer->getStartLocation()->getName()

            ],
            OptimizeKeyRequestInterface::VEHICLES_END_ADDRESS => [
                OptimizeKeyRequestInterface::VEHICLES_END_ADDRESS_LOCATION_ID => $this->grapphopperTourTransfer->getEndLocation()->getLocationId(),
                OptimizeKeyRequestInterface::VEHICLES_END_ADDRESS_LAT => $this->grapphopperTourTransfer->getEndLocation()->getAddressLat(),
                OptimizeKeyRequestInterface::VEHICLES_END_ADDRESS_LON => $this->grapphopperTourTransfer->getEndLocation()->getAddressLng(),
                OptimizeKeyRequestInterface::VEHICLES_END_ADDRESS_STREET_HINT => $this->grapphopperTourTransfer->getEndLocation()->getName()
            ],
            OptimizeKeyRequestInterface::VEHICLES_RETURN_TO_DEPOT => true,
            OptimizeKeyRequestInterface::VEHICLES_MAX_JOBS => $this->grapphopperTourTransfer->getStartLocation()->getItemCount()
        ];

        return $vehicles;
    }

    /**
     * @return array
     */
    protected function createVehicleTypeArray(): array
    {
        $vehiclesTypes = [
            OptimizeKeyRequestInterface::VEHICLE_TYPES_TYPE_ID => $this->grapphopperTourTransfer->getVehicleTypeName(),
            OptimizeKeyRequestInterface::VEHICLE_TYPES_CAPACITY => [
                $this->grapphopperTourTransfer->getStartLocation()->getItemCount()
            ],
            OptimizeKeyRequestInterface::VEHICLE_TYPES_PROFILE => $this->grapphopperTourTransfer->getVehicleCategoryProfile(),
        ];

        return $vehiclesTypes;
    }

    /**
     * @param array $orderAddress
     * @return array
     */
    protected function createServicesArray(array $orderAddress): array
    {
        $address = [
            OptimizeKeyRequestInterface::SERVICES_ID => sprintf(
                '%s%d',
                static::LOCATION_PREFIX,
                $orderAddress[OptimizeKeyRequestInterface::SERVICES_ID]
            ),
            OptimizeKeyRequestInterface::SERVICES_NAME => $orderAddress[OptimizeKeyRequestInterface::SERVICES_NAME],
            OptimizeKeyRequestInterface::SERVICES_ADDRESS => [
                OptimizeKeyRequestInterface::SERVICES_ADDRESS_LOCATION_ID => $orderAddress[OptimizeKeyRequestInterface::SERVICES_ADDRESS_LOCATION_ID],
                OptimizeKeyRequestInterface::SERVICES_ADDRESS_LAT => $orderAddress[OptimizeKeyRequestInterface::SERVICES_ADDRESS_LAT],
                OptimizeKeyRequestInterface::SERVICES_ADDRESS_LON => $orderAddress[OptimizeKeyRequestInterface::SERVICES_ADDRESS_LON],
                OptimizeKeyRequestInterface::SERVICES_ADDRESS_STREET_HINT => $orderAddress[OptimizeKeyRequestInterface::SERVICES_ADDRESS_STREET_HINT]
            ],
            OptimizeKeyRequestInterface::SERVICES_TYPE => GraphhopperConstants::SERVICES_TYPE,
            OptimizeKeyRequestInterface::SERVICES_SIZE => [
                $orderAddress[OptimizeKeyRequestInterface::SERVICES_SIZE]
            ],
            OptimizeKeyRequestInterface::SERVICES_TIME_WINDOWS => [
                [
                    OptimizeKeyRequestInterface::SERVICES_TIME_WINDOWS_EARLIEST => $orderAddress[OptimizeKeyRequestInterface::SERVICES_TIME_WINDOWS_EARLIEST],
                    OptimizeKeyRequestInterface::SERVICES_TIME_WINDOWS_LATEST => $orderAddress[OptimizeKeyRequestInterface::SERVICES_TIME_WINDOWS_LATEST]
                ]
            ],
            OptimizeKeyRequestInterface::SERVICES_DURATION => GraphhopperConstants::SERVICES_DURATION
        ];

        return $address;
    }

    /**
     * @return array
     */
    protected function createObjectivesArray(): array
    {
        $objectives[] = [
            OptimizeKeyRequestInterface::OBJECTIVES_TYPE => GraphhopperConstants::OBJECTIVES_TYPE,
            OptimizeKeyRequestInterface::OBJECTIVES_VALUE => GraphhopperConstants::OBJECTIVES_VALUE_VEHICLES
        ];

        $objectives[] = [
            OptimizeKeyRequestInterface::OBJECTIVES_TYPE => GraphhopperConstants::OBJECTIVES_TYPE,
            OptimizeKeyRequestInterface::OBJECTIVES_VALUE => GraphhopperConstants::OBJECTIVES_VALUE_COMPLETION_TIME
        ];

        return $objectives;
    }

    /**
     * @return array
     */
    protected function createConfigurationArray(): array
    {
        $configuration = [
            OptimizeKeyRequestInterface::CONFIGURATION_ROUTING => [
                OptimizeKeyRequestInterface::CONFIGURATION_ROUTING_CALC_POINTS => true
            ]
        ];

        return $configuration;
    }

    /**
     * @param \Generated\Shared\Transfer\HttpResponseTransfer $responseTransfer
     * @return \stdClass
     * @throws \Pyz\Zed\Graphhopper\Business\Exception\OptimizeJobTerminatedException
     */
    protected function callJobServicePeriodical(HttpResponseTransfer $responseTransfer): stdClass
    {
        $httpRequestTransfer = $this
            ->createJobHttpRequest(
                $responseTransfer
            );

        $jobFinished = false;

        $responseJson = new stdClass();

        while ($jobFinished === false) {
            $response = $this
                ->httpRequestService
                ->sendRequest(
                    $httpRequestTransfer
                );

            $this
                ->httpRequestBridgeFacade
                ->createHttpRequestLogEntry(
                    $httpRequestTransfer,
                    $response
                );

            if ($response->getErrors()->count() > 0) {
                throw new OptimizeJobTerminatedException(
                    sprintf(
                        OptimizeJobTerminatedException::MESSAGE,
                        $this->grapphopperTourTransfer->getTourId()
                    )
                );
            }

            $responseJson = json_decode($response->getBody());

            if (strtolower($responseJson->{OptimizeKeyResponseInterface::STATUS}) === strtolower(GraphhopperConstants::STATUS_FINISHED)) {
                $jobFinished = true;
            }

            usleep(500000);
        }

        return $responseJson;
    }

    /**
     * @param \Generated\Shared\Transfer\HttpResponseTransfer $responseTransfer
     * @return \Generated\Shared\Transfer\HttpRequestTransfer
     */
    protected function createJobHttpRequest(HttpResponseTransfer $responseTransfer): HttpRequestTransfer
    {
        $responseJson = json_decode($responseTransfer->getBody());
        $jobId = $responseJson
            ->{OptimizeKeyResponseInterface::JOB_ID};

        $httpRequestTransfer = new HttpRequestTransfer();

        $options = new HttpRequestOptionsTransfer();
        $options
            ->setQuery([
                OptimizeKeyRequestInterface::OPTIMIZE_API_KEY => $this->config->getGraphhopperApiKey()
            ]);

        $httpRequestTransfer
            ->setOptions($options)
            ->setMethod(PyzHttpRequestTableMap::COL_REQUEST_METHOD_GET)
            ->setUri(
                sprintf(
                    GraphhopperConstants::URL_OPTIMIZE_RESULTS,
                    $jobId
                )
            );

        return $httpRequestTransfer;
    }

    /**
     * @param \stdClass $orderResponse
     * @return array
     * @throws \Pyz\Zed\Graphhopper\Business\Exception\NoVehicleRouteFoundException
     */
    protected function sortStopsByAddresses(stdClass $orderResponse): array
    {
        $solution = $orderResponse
            ->{OptimizeKeyResponseInterface::SOLUTION};

        $vehicleRoutes = $solution
            ->{OptimizeKeyResponseInterface::SOLUTION_ROUTES};

        if (is_array($vehicleRoutes) !== true) {
            throw new NoVehicleRouteFoundException(
                NoVehicleRouteFoundException::MESSAGE
            );
        }

        $vehicleRoute = reset($vehicleRoutes);

        $sortedOrders = [];

        foreach ($vehicleRoute->{OptimizeKeyResponseInterface::SOLUTION_ROUTES_ACTIVITIES} as $activity) {
            if ($activity->{OptimizeKeyResponseInterface::SOLUTION_ROUTES_ACTIVITIES_TYPE} !== GraphhopperConstants::SERVICES_TYPE) {
                continue;
            }

            $idOrder = (int)str_replace(
                static::LOCATION_PREFIX,
                '',
                $activity->{OptimizeKeyResponseInterface::SOLUTION_ROUTES_ACTIVITIES_ID}
            );

            $sortedOrders[] = $idOrder;
        }

        $ordersUnassigned = $solution
            ->{OptimizeKeyResponseInterface::SOLUTION_NO_UNASSIGNED};

        $unassignedTimeslotOrderIdMap = [];
        if ($ordersUnassigned > 0) {

            $unassignedServices = $solution
                ->{OptimizeKeyResponseInterface::SOLUTION_UNASSIGNED}
                ->{OptimizeKeyResponseInterface::SOLUTION_UNASSIGNED_SERVICES};

            foreach($unassignedServices as $unassignedService){
                $orderId = $this->getOrderIdFromLocationString($unassignedService);
                $order = $this->idToStopMap[$orderId];

                if(array_key_exists($order->getTimeslotId(), $unassignedTimeslotOrderIdMap) !== true)
                {
                    $unassignedTimeslotOrderIdMap[$order->getTimeslotId()] = [];
                }
                $unassignedTimeslotOrderIdMap[$order->getTimeslotId()][] = $orderId;
            }
        }

        $sortedOrders = $this->addUnassignedOrdersToSortedOrders($unassignedTimeslotOrderIdMap, $sortedOrders);

        return $sortedOrders;
    }


    /**
     * @param array $sortedOrders
     * @param OrderTransfer[] $orderTransfers
     */
    protected function updateSortingInStops(array $sortedOrders): void
    {
        $sortedStops = [];
        foreach ($sortedOrders as $orderId)
        {
            $sortedStops[] = $this->idToStopMap[$orderId];
        }

        $this->grapphopperTourTransfer->setStops(new \ArrayObject($sortedStops));
    }

    /**
     * @param string $locationString
     * @return int
     */
    protected function getOrderIdFromLocationString(string $locationString): int {
        return (int) str_replace(
            static::LOCATION_PREFIX,
            '',
            $locationString
        );
    }

    /**
     * @param array $unassignedOrders
     * @param array $sortedOrders
     * @return array
     */
    protected function addUnassignedOrdersToSortedOrders(array $unassignedOrders, array $sortedOrders){

        $ordersPerTimeslots = [];
        $offset = 1;

        foreach($sortedOrders as $orderId){
            $order = $this->idToStopMap[$orderId];
            $timeSlotId = $order->getTimeslotId();

            if(array_key_exists($timeSlotId, $ordersPerTimeslots) !== true){
                $ordersPerTimeslots[$timeSlotId] = 0;
            }
            $ordersPerTimeslots[$timeSlotId] += 1;


            if(array_key_exists($timeSlotId, $unassignedOrders) === true && ($ordersPerTimeslots[$timeSlotId] + count($unassignedOrders[$timeSlotId]) == count($this->timeslotOrderIdMap[$timeSlotId]))){
                array_splice($sortedOrders, $offset, 0, $unassignedOrders[$timeSlotId]);
                $offset += count($unassignedOrders[$timeSlotId]);
                unset($unassignedOrders[$timeSlotId]);
                continue;
            }

            $offset += 1;
        }

        foreach ($unassignedOrders as $timeslotId => $orders)
        {
            array_merge($sortedOrders, $orders);
        }

        return $sortedOrders;
    }
}
