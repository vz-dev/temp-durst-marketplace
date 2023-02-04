<?php
/**
 * Durst - project - TourOrderSorterTest.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-01-07
 * Time: 21:12
 */

namespace PyzTest\Functional\Zed\Graphhopper\Business\Model;


use Codeception\Test\Unit;
use DateTime;
use Generated\Shared\Transfer\GraphhopperStopTransfer;
use Generated\Shared\Transfer\GraphhopperTourTransfer;
use Generated\Shared\Transfer\HttpRequestErrorTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Service\HttpRequest\HttpRequestService;
use Pyz\Zed\Graphhopper\Business\Exception\GenerateOptimizeJobFailException;
use Pyz\Zed\Graphhopper\Business\Exception\OptimizeJobTerminatedException;
use Pyz\Zed\Graphhopper\Business\Model\TourOrderSorter;
use Pyz\Zed\Graphhopper\Dependency\GraphhopperToHttpRequestBridge;
use Pyz\Zed\Graphhopper\GraphhopperConfig;
use Pyz\Zed\HttpRequest\Business\HttpRequestFacade;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;

class TourOrderSorterTest extends Unit
{
    public const TIMESLOT_1_ID = 789;
    public const TIMESLOT_2_ID = 999;

    public const START_END_VEHICLE_TYPE_ID = 99;
    public const START_END_NAME = 'Oskar Jäger Str. 173';
    public const START_END_LOCATION_ID = 'Köln';
    public const START_END_ADDRESS_LAT = '9.9999';
    public const START_END_ADDRESS_LNG = '5.5555';
    public const START_END_ITEM_COUNT = 25;

    public const STOP_1_ID = 80;
    public const STOP_1_NAME = 'Friedhofstraßfe 21';
    public const STOP_1_LOCATION_ID = 'Friedhofstraßfe 21';
    public const STOP_1_LAT = 50.7491875;
    public const STOP_1_LNG = 6.4231034;
    public const STOP_1_EARLIEST = 1576008000;
    public const STOP_1_LATEST = 1576015200;
    public const STOP_1_ITEMS = 8;

    public const STOP_2_ID = 81;
    public const STOP_2_NAME = 'Trierer Straße 32';
    public const STOP_2_LOCATION_ID = 'Trierer Straße 32';
    public const STOP_2_LAT = 50.74389405;
    public const STOP_2_LNG = 6.7059748896787;
    public const STOP_2_EARLIEST = 1576008000;
    public const STOP_2_LATEST = 1576015200;
    public const STOP_2_ITEMS = 3;

    public const STOP_3_ID = 82;
    public const STOP_3_NAME = 'Petersplatz 1';
    public const STOP_3_LOCATION_ID = 'Petersplatz 1';
    public const STOP_3_LAT = 50.74389405;
    public const STOP_3_LNG = 6.7059748896787;
    public const STOP_3_EARLIEST = 1576008000;
    public const STOP_3_LATEST = 1576015200;
    public const STOP_3_ITEMS = 4;

    public const STOP_4_ID = 83;
    public const STOP_4_NAME = 'Trierer Straße 27';
    public const STOP_4_LOCATION_ID = 'Trierer Straße 27';
    public const STOP_4_LAT = 50.70137205;
    public const STOP_4_LNG = 6.8711314989724;
    public const STOP_4_EARLIEST = 1576008000;
    public const STOP_4_LATEST = 1576015200;
    public const STOP_4_ITEMS = 3;

    public const STOP_5_ID = 88;
    public const STOP_5_NAME = 'Fischerberg 10';
    public const STOP_5_LOCATION_ID = 'Fischerberg 10';
    public const STOP_5_LAT = 50.7013105;
    public const STOP_5_LNG = 6.87113158798;
    public const STOP_5_EARLIEST = 1576008000;
    public const STOP_5_LATEST = 1576015200;
    public const STOP_5_ITEMS = 7;

    /**
     * @var TourOrderSorter
     */
    protected $tourOrderSorter;

    /**
     * @var MockObject|HttpRequestService
     */
    protected $httpRequestService;

    /**
     * @var MockObject|GraphhopperToHttpRequestBridge
     */
    protected $httpRequestBridge;

    /**
     * @var GraphhopperConfig
     */
    protected $config;


    protected function _before(){
        $this->httpRequestService = $this->createHttpRequestServiceMock();
        $this->httpRequestBridge = $this->createHttpRequestBridgeMock();
        $this->config = $this->createConfigMock();

        $this->tourOrderSorter = new TourOrderSorter(
            $this->httpRequestService,
            $this->httpRequestBridge,
            $this->config
        );
    }

    public function testThrowsRequiredTransferPropertyExceptionWhenMissingStart()
    {
        $this
            ->expectException(RequiredTransferPropertyException::class);

        $ghTour = new GraphhopperTourTransfer();
        $ghTour->setEndLocation(new GraphhopperStopTransfer());
        $ghTour->setStops(new \ArrayObject(new GraphhopperStopTransfer()));

        $this->tourOrderSorter->orderTourOrders(new GraphhopperTourTransfer());
    }

    public function testThrowsRequiredTransferPropertyExceptionWhenMissingEnd()
    {

        $this
            ->expectException(RequiredTransferPropertyException::class);

        $ghTour = new GraphhopperTourTransfer();
        $ghTour->setStartLocation(new GraphhopperStopTransfer());
        $ghTour->setStops(new \ArrayObject(new GraphhopperStopTransfer()));

        $this->tourOrderSorter->orderTourOrders(new GraphhopperTourTransfer());
    }

    public function testThrowsRequiredTransferPropertyExceptionWhenMissingStops()
    {

        $this
            ->expectException(RequiredTransferPropertyException::class);

        $ghTour = new GraphhopperTourTransfer();
        $ghTour->setStartLocation(new GraphhopperStopTransfer());
        $ghTour->setEndLocation(new GraphhopperStopTransfer());

        $this->tourOrderSorter->orderTourOrders($ghTour);
    }

    public function testThrowGenerateOptimizeJobFailException()
    {
        $this
            ->expectException(GenerateOptimizeJobFailException::class);

        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->willReturn(
                $this->createHttpResponseWithErrors()
            );

        $this->tourOrderSorter->orderTourOrders($this->createTestGraphhopperTransfer());
    }

    public function testThrowOptimizeJobTerminatedException()
    {
        $this
            ->expectException(OptimizeJobTerminatedException::class);

        $this
            ->httpRequestBridge
            ->expects($this->atLeastOnce())
            ->method('createHttpRequestLogEntry');

        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->will(
                $this->onConsecutiveCalls(
                    $this->createJobHttpResponse(),
                    $this->createHttpResponseWithErrors()
                )
            );

        $this->tourOrderSorter->orderTourOrders($this->createTestGraphhopperTransfer());
    }

    public function testSortedGraphhopperTourContainsCorrectAmountOfStops()
    {
        $this
            ->httpRequestBridge
            ->expects($this->atLeastOnce())
            ->method('createHttpRequestLogEntry');

        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->will(
                $this->onConsecutiveCalls(
                    $this->createJobHttpResponse(),
                    $this->createOptimizedHttpResponse()
                )
            );

        $ghTour = $this->tourOrderSorter->orderTourOrders($this->createTestGraphhopperTransfer());

        self::assertSame($this->createTestGraphhopperTransfer()->getStops()->count(), $ghTour->getStops()->count());
    }

    public function testStopsAreOrderedInOrderPassedByTourOptimizer()
    {
        $this
            ->httpRequestBridge
            ->expects($this->atLeastOnce())
            ->method('createHttpRequestLogEntry');

        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->will(
                $this->onConsecutiveCalls(
                    $this->createJobHttpResponse(),
                    $this->createOptimizedHttpResponse()
                )
            );

        $ghTour = $this->tourOrderSorter->orderTourOrders($this->createTestGraphhopperTransfer());

        $this->assertSame(self::STOP_1_ID, $ghTour->getStops()[0]->getId());
        $this->assertSame(self::STOP_2_ID, $ghTour->getStops()[1]->getId());
        $this->assertSame(self::STOP_3_ID, $ghTour->getStops()[2]->getId());
        $this->assertSame(self::STOP_5_ID, $ghTour->getStops()[3]->getId());
        $this->assertSame(self::STOP_4_ID, $ghTour->getStops()[4]->getId());
    }

    public function testUnassignedStopsAreAddedAfterLastTimeSlotStop()
    {
        $this
            ->httpRequestBridge
            ->expects($this->atLeastOnce())
            ->method('createHttpRequestLogEntry');

        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->will(
                $this->onConsecutiveCalls(
                    $this->createJobHttpResponse(),
                    $this->createOptimizedWithUnassignedStopsHttpResponse()
                )
            );

        $ghTour = $this->tourOrderSorter->orderTourOrders($this->createTestGraphhopperTransfer());

        $this->assertSame(self::STOP_2_ID, $ghTour->getStops()[2]->getId());
    }



    /**
     * @return HttpRequestService|MockObject
     */
    protected function createHttpRequestServiceMock()
    {
        return $this
            ->getMockBuilder(HttpRequestService::class)
            ->setMethods([
                'sendRequest'
            ])
            ->getMock();
    }

    /**
     * @return GraphhopperToHttpRequestBridge|MockObject
     */
    protected function createHttpRequestBridgeMock()
    {
        return $this
            ->getMockBuilder(GraphhopperToHttpRequestBridge::class)
            ->setConstructorArgs([
                $this->createHttpRequestFacadeMock(),
            ])
            ->getMock();
    }

    protected function createHttpRequestFacadeMock()
    {
        return $this
            ->getMockBuilder(HttpRequestFacade::class)
            ->getMock();
    }

    /**
     * @return GraphhopperConfig|MockObject
     */
    protected function createConfigMock()
    {
        return $this
            ->getMockBuilder(GraphhopperConfig::class)
            ->setMethods(['getProjectTimeZone'])
            ->getMock();
    }

    protected function createHttpResponseTransfer() : HttpResponseTransfer
    {
        $hrt = new HttpResponseTransfer();
        return $hrt
            ->setBody('test')
            ->setCode(200);
    }

    protected function createJobHttpResponse() : HttpResponseTransfer
    {
        $hrt = new HttpResponseTransfer();
        return $hrt
            ->setBody('{"job_id":"2574f4e3-a48e-4ab0-ae12-aab664b5f8b2"}');
    }

    protected function createOptimizedHttpResponse() : HttpResponseTransfer
    {
        $hrt = new HttpResponseTransfer();
        return $hrt
            ->setBody(
                '{
                  "copyrights": ["GraphHopper", "OpenStreetMap contributors"],
                  "job_id": "2574f4e3-a48e-4ab0-ae12-aab664b5f8b2",
                  "status": "finished",
                  "waiting_time_in_queue": 0,
                  "processing_time": 333,
                  "solution": {
                    "costs": 12608339,
                    "distance": 131287,
                    "time": 6965,
                    "transport_time": 6965,
                    "completion_time": 1576012771,
                    "max_operation_time": 1576012771,
                    "waiting_time": 1576005206,
                    "service_duration": 600,
                    "preparation_time": 0,
                    "no_vehicles": 1,
                    "no_unassigned": 0,
                    "routes": [{
                      "vehicle_id": "vehicle-1",
                      "distance": 131287,
                      "transport_time": 6965,
                      "completion_time": 1576012771,
                      "waiting_time": 1576005206,
                      "service_duration": 600,
                      "preparation_time": 0,
                      "activities": [{
                        "type": "start",
                        "location_id": "K\\u00f6ln",
                        "address": {
                          "location_id": "K\\u00f6ln",
                          "lat": 50.9474595,
                          "lon": 6.9048215
                        },
                        "end_time": 0,
                        "end_date_time": null,
                        "distance": 0,
                        "driving_time": 0,
                        "preparation_time": 0,
                        "waiting_time": 0,
                        "load_after": [35]
                      }, {
                        "type": "delivery",
                        "id": "location-80",
                        "location_id": "Friedhofstra\\u00dfe 21",
                        "address": {
                          "location_id": "Friedhofstra\\u00dfe 21",
                          "lat": 50.7491875,
                          "lon": 6.4231034
                        },
                        "arr_time": 2794,
                        "arr_date_time": null,
                        "end_time": 1576008120,
                        "end_date_time": null,
                        "waiting_time": 1576005206,
                        "distance": 50427,
                        "driving_time": 2794,
                        "preparation_time": 0,
                        "load_before": [35],
                        "load_after": [28]
                      }, {
                        "type": "delivery",
                        "id": "location-81",
                        "location_id": "Trierer Stra\\u00dfe 32",
                        "address": {
                          "location_id": "Trierer Stra\\u00dfe 32",
                          "lat": 50.74389405,
                          "lon": 6.7059748896787
                        },
                        "arr_time": 1576009598,
                        "arr_date_time": null,
                        "end_time": 1576009718,
                        "end_date_time": null,
                        "waiting_time": 0,
                        "distance": 77844,
                        "driving_time": 4272,
                        "preparation_time": 0,
                        "load_before": [28],
                        "load_after": [21]
                      }, {
                       "type": "delivery",
                        "id": "location-82",
                        "location_id": "Petersplatz 1",
                        "address": {
                          "location_id": "Petersplatz 1",
                          "lat": 50.74389405,
                          "lon": 6.7059748896787
                        },
                        "arr_time": 1576009718,
                        "arr_date_time": null,
                        "end_time": 1576009838,
                        "end_date_time": null,
                        "waiting_time": 0,
                        "distance": 77844,
                        "driving_time": 4272,
                        "preparation_time": 0,
                        "load_before": [21],
                        "load_after": [14]
                      }, {
                        "type": "delivery",
                        "id": "location-88",
                        "location_id": "Fischerberg 10",
                        "address": {
                          "location_id": "Fischerberg 10",
                          "lat": 50.7013105,
                          "lon": 6.87113158798
                        },
                        "arr_time": 1576010745,
                        "arr_date_time": null,
                        "end_time": 1576010865,
                        "end_date_time": null,
                        "waiting_time": 0,
                        "distance": 92625,
                        "driving_time": 5179,
                        "preparation_time": 0,
                        "load_before": [14],
                        "load_after": [7]
                      }, {
                        "type": "delivery",
                        "id": "location-83",
                        "location_id": "Trierer Stra\\u00dfe 27",
                        "address": {
                          "location_id": "Trierer Stra\\u00dfe 27",
                          "lat": 50.70137205,
                          "lon": 6.8711314989724
                        },
                        "arr_time": 1576010865,
                        "arr_date_time": null,
                        "end_time": 1576010985,
                        "end_date_time": null,
                        "waiting_time": 0,
                        "distance": 92627,
                        "driving_time": 5179,
                        "preparation_time": 0,
                        "load_before": [7],
                        "load_after": [0]
                      }, {
                        "type": "end",
                        "location_id": "K\\u00f6ln",
                        "address": {
                          "location_id": "K\\u00f6ln",
                          "lat": 50.9474595,
                          "lon": 6.9048215
                        },
                        "arr_time": 1576012771,
                        "arr_date_time": null,
                        "distance": 131287,
                        "driving_time": 6965,
                        "preparation_time": 0,
                        "waiting_time": 0,
                        "load_before": [0]
                      }]
                    }],
                    "unassigned": {
                      "services": [],
                      "shipments": [],
                      "breaks": [],
                      "details": []
                    }
                  }
                }'
            )
            ->setCode(200);
    }

        protected function createOptimizedWithUnassignedStopsHttpResponse() : HttpResponseTransfer
        {
            $hrt = new HttpResponseTransfer();
            return $hrt
                ->setBody(
                    '{
                      "copyrights": ["GraphHopper", "OpenStreetMap contributors"],
                      "job_id": "2574f4e3-a48e-4ab0-ae12-aab664b5f8b2",
                      "status": "finished",
                      "waiting_time_in_queue": 0,
                      "processing_time": 333,
                      "solution": {
                        "costs": 12608339,
                        "distance": 131287,
                        "time": 6965,
                        "transport_time": 6965,
                        "completion_time": 1576012771,
                        "max_operation_time": 1576012771,
                        "waiting_time": 1576005206,
                        "service_duration": 600,
                        "preparation_time": 0,
                        "no_vehicles": 1,
                        "no_unassigned": 1,
                        "routes": [{
                          "vehicle_id": "vehicle-1",
                          "distance": 131287,
                          "transport_time": 6965,
                          "completion_time": 1576012771,
                          "waiting_time": 1576005206,
                          "service_duration": 600,
                          "preparation_time": 0,
                          "activities": [{
                            "type": "start",
                            "location_id": "K\\u00f6ln",
                            "address": {
                              "location_id": "K\\u00f6ln",
                              "lat": 50.9474595,
                              "lon": 6.9048215
                            },
                            "end_time": 0,
                            "end_date_time": null,
                            "distance": 0,
                            "driving_time": 0,
                            "preparation_time": 0,
                            "waiting_time": 0,
                            "load_after": [35]
                          }, {
                            "type": "delivery",
                            "id": "location-80",
                            "location_id": "Friedhofstra\\u00dfe 21",
                            "address": {
                              "location_id": "Friedhofstra\\u00dfe 21",
                              "lat": 50.7491875,
                              "lon": 6.4231034
                            },
                            "arr_time": 2794,
                            "arr_date_time": null,
                            "end_time": 1576008120,
                            "end_date_time": null,
                            "waiting_time": 1576005206,
                            "distance": 50427,
                            "driving_time": 2794,
                            "preparation_time": 0,
                            "load_before": [35],
                            "load_after": [28]
                          }, {
                           "type": "delivery",
                            "id": "location-82",
                            "location_id": "Petersplatz 1",
                            "address": {
                              "location_id": "Petersplatz 1",
                              "lat": 50.74389405,
                              "lon": 6.7059748896787
                            },
                            "arr_time": 1576009718,
                            "arr_date_time": null,
                            "end_time": 1576009838,
                            "end_date_time": null,
                            "waiting_time": 0,
                            "distance": 77844,
                            "driving_time": 4272,
                            "preparation_time": 0,
                            "load_before": [21],
                            "load_after": [14]
                          }, {
                            "type": "delivery",
                            "id": "location-88",
                            "location_id": "Fischerberg 10",
                            "address": {
                              "location_id": "Fischerberg 10",
                              "lat": 50.7013105,
                              "lon": 6.87113158798
                            },
                            "arr_time": 1576010745,
                            "arr_date_time": null,
                            "end_time": 1576010865,
                            "end_date_time": null,
                            "waiting_time": 0,
                            "distance": 92625,
                            "driving_time": 5179,
                            "preparation_time": 0,
                            "load_before": [14],
                            "load_after": [7]
                          }, {
                            "type": "delivery",
                            "id": "location-83",
                            "location_id": "Trierer Stra\\u00dfe 27",
                            "address": {
                              "location_id": "Trierer Stra\\u00dfe 27",
                              "lat": 50.70137205,
                              "lon": 6.8711314989724
                            },
                            "arr_time": 1576010865,
                            "arr_date_time": null,
                            "end_time": 1576010985,
                            "end_date_time": null,
                            "waiting_time": 0,
                            "distance": 92627,
                            "driving_time": 5179,
                            "preparation_time": 0,
                            "load_before": [7],
                            "load_after": [0]
                          }, {
                            "type": "end",
                            "location_id": "K\\u00f6ln",
                            "address": {
                              "location_id": "K\\u00f6ln",
                              "lat": 50.9474595,
                              "lon": 6.9048215
                            },
                            "arr_time": 1576012771,
                            "arr_date_time": null,
                            "distance": 131287,
                            "driving_time": 6965,
                            "preparation_time": 0,
                            "waiting_time": 0,
                            "load_before": [0]
                          }]
                        }],
                        "unassigned": {
                          "services": [
                            "location-81"
                          ],
                          "shipments": [],
                          "breaks": [],
                          "details": []
                        }
                      }
                    }'
                )
                ->setCode(200);
        }

    protected function createHttpResponseWithErrors() : HttpResponseTransfer
    {
        return $this->createHttpResponseTransfer()->addErrors(new HttpRequestErrorTransfer());
    }

    protected function createTestGraphhopperTransfer() : GraphhopperTourTransfer
    {
        $ghTour = new GraphhopperTourTransfer();
        return $ghTour
            ->setStartLocation($this->createGraphopperStartOrEnd())
            ->setEndLocation($this->createGraphopperStartOrEnd())
            ->setStops($this->createTestGraphhopperStops());
    }

    protected function createGraphopperStartOrEnd() : GraphhopperStopTransfer
    {
        $ghStop = new GraphhopperStopTransfer();

        return $ghStop
            ->setVehicleId(static::START_END_VEHICLE_TYPE_ID)
            ->setName(static::START_END_NAME)
            ->setLocationId(static::START_END_LOCATION_ID)
            ->setAddressLat(static::START_END_ADDRESS_LAT)
            ->setAddressLng(static::START_END_ADDRESS_LNG);
    }

    protected function createTestGraphhopperStops() : \ArrayObject
    {
        $stops = new \ArrayObject();

        $stop = new GraphhopperStopTransfer();
        $stop
            ->setId(static::STOP_1_ID)
            ->setLocationId(static::STOP_1_LOCATION_ID)
            ->setAddressLat(static::STOP_1_LAT)
            ->setAddressLng(static::STOP_1_LNG)
            ->setConstraintEarliest(DateTime::createFromFormat('U', static::STOP_1_EARLIEST))
            ->setConstraintLatest(DateTime::createFromFormat('U', static::STOP_1_LATEST))
            ->setItemCount(static::STOP_1_ITEMS)
            ->setTimeslotId(static::TIMESLOT_1_ID);

        $stops->append($stop);

        $stop = new GraphhopperStopTransfer();
        $stop
            ->setId(static::STOP_2_ID)
            ->setLocationId(static::STOP_2_LOCATION_ID)
            ->setAddressLat(static::STOP_2_LAT)
            ->setAddressLng(static::STOP_2_LNG)
            ->setConstraintEarliest(DateTime::createFromFormat('U', static::STOP_2_EARLIEST))
            ->setConstraintLatest(DateTime::createFromFormat('U', static::STOP_2_LATEST))
            ->setItemCount(static::STOP_2_ITEMS)
            ->setTimeslotId(static::TIMESLOT_1_ID);

        $stops->append($stop);

        $stop = new GraphhopperStopTransfer();
        $stop
            ->setId(static::STOP_3_ID)
            ->setLocationId(static::STOP_3_LOCATION_ID)
            ->setAddressLat(static::STOP_3_LAT)
            ->setAddressLng(static::STOP_3_LNG)
            ->setConstraintEarliest(DateTime::createFromFormat('U', static::STOP_3_EARLIEST))
            ->setConstraintLatest(DateTime::createFromFormat('U', static::STOP_3_LATEST))
            ->setItemCount(static::STOP_3_ITEMS)
            ->setTimeslotId(static::TIMESLOT_1_ID);

        $stops->append($stop);

        $stop = new GraphhopperStopTransfer();
        $stop
            ->setId(static::STOP_4_ID)
            ->setLocationId(static::STOP_4_LOCATION_ID)
            ->setAddressLat(static::STOP_4_LAT)
            ->setAddressLng(static::STOP_4_LNG)
            ->setConstraintEarliest(DateTime::createFromFormat('U', static::STOP_4_EARLIEST))
            ->setConstraintLatest(DateTime::createFromFormat('U', static::STOP_4_LATEST))
            ->setItemCount(static::STOP_4_ITEMS)
            ->setTimeslotId(static::TIMESLOT_2_ID);

        $stops->append($stop);

        $stop = new GraphhopperStopTransfer();
        $stop
            ->setId(static::STOP_5_ID)
            ->setLocationId(static::STOP_5_LOCATION_ID)
            ->setAddressLat(static::STOP_5_LAT)
            ->setAddressLng(static::STOP_5_LNG)
            ->setConstraintEarliest(DateTime::createFromFormat('U', static::STOP_5_EARLIEST))
            ->setConstraintLatest(DateTime::createFromFormat('U', static::STOP_5_LATEST))
            ->setItemCount(static::STOP_5_ITEMS)
            ->setTimeslotId(static::TIMESLOT_2_ID);

        $stops->append($stop);

        return $stops;
    }


}
