<?php
/**
 * Durst - project - GeocodingTest.phpp.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-12-16
 * Time: 20:00
 */

namespace PyzTest\Functional\Zed\Graphhopper\Business\Model;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\HttpResponseTransfer;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Service\HttpRequest\HttpRequestService;
use Pyz\Zed\Graphhopper\Business\Exception\LocationNotFoundException;
use Pyz\Zed\Graphhopper\Business\Model\Geocoding;
use Pyz\Zed\Graphhopper\Business\Model\GeocodingInterface;
use Pyz\Zed\Graphhopper\Dependency\GraphhopperToHttpRequestBridge;
use Pyz\Zed\Graphhopper\GraphhopperConfig;
use Pyz\Zed\HttpRequest\Business\HttpRequestFacade;
use Pyz\Zed\Tour\TourConfig;

class GeocodingTest extends Unit
{
    public const ADDRESS_STRING_FORMAT = '%s %s %s';
    public const ADDRESS_ADDRESS_1 = 'Oskar Jäger Str. 173';
    public const ADDRESS_CITY = 'Köln';
    public const ADDRESS_ZIP_CODE = '50825';

    public const ADDRESS_EXPECTED_LAT = '52.5170365';
    public const ADDRESS_EXPECTED_LNG = '13.3888599';

    public const ADDRESS_FALSE_TEST_LOCATION = 'NOWHERE-INTERESTING';

    /**
     * @var GeocodingInterface
     */
    protected $geocoding;

    /**
     * @var HttpRequestService|MockObject
     */
    protected $httpRequestService;

    /**
     * @var GraphhopperToHttpRequestBridge|MockObject
     */
    protected $httpRequestBridgeFacade;

    /**
     * @var TourConfig|MockObject
     */
    protected $config;

    protected function _before()
    {
        $this->httpRequestService = $this->createHttpRequestServiceMock();
        $this->httpRequestBridgeFacade = $this->createHttpRequestBridgeFacadeMock();
        $this->config = $this->createGraphhopperConfig();

        $this->geocoding = new Geocoding(
            $this->httpRequestService,
            $this->httpRequestBridgeFacade,
            $this->config
        );
    }

    protected function _after()
    {
    }

    public function testGeocoderGraphhopperCoordinatesTransferSetLatLngQuery()
    {
        $graphhopperCoordinatesTransfer = $this->geocoding->getCoordinatesForAddressString($this->createAddressString());

        $this->assertNotNull(
            $graphhopperCoordinatesTransfer->getLat()
        );

        $this->assertNotNull(
            $graphhopperCoordinatesTransfer->getLat()
        );

        $this->assertNotNull(
            $graphhopperCoordinatesTransfer->getQuery()
        );
    }

    public function testGeocoderHydratesCorrectCoordinates()
    {
        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->willReturn($this->createHttpResponseWithHit());

        $graphhopperCoordinatesTransfer = $this->geocoding->getCoordinatesForAddressString($this->createAddressString());

        $this->assertEquals(
            self::ADDRESS_EXPECTED_LAT,
            $graphhopperCoordinatesTransfer->getLat()
        );

        $this->assertEquals(
            self::ADDRESS_EXPECTED_LNG,
            $graphhopperCoordinatesTransfer->getLng()
        );

    }

    public function testGeocoderReturnsNoLocationFoundException()
    {
        $this->expectException(
            LocationNotFoundException::class
        );

        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->willReturn($this->createHttpResponseNoHits());

        $this->geocoding->getCoordinatesForAddressString(self::ADDRESS_FALSE_TEST_LOCATION);
    }

    /**
     * @return MockObject|HttpRequestService
     */
    protected function createHttpRequestServiceMock()
    {
        return $this
            ->getMockBuilder(HttpRequestService::class)
            ->setMethods([
                'sendRequest',
            ])
            ->getMock();
    }

    /**
     * @return MockObject|GraphhopperToHttpRequestBridge
     */
    protected function createHttpRequestBridgeFacadeMock()
    {
        return $this
            ->getMockBuilder(GraphhopperToHttpRequestBridge::class)
            ->setConstructorArgs([
                $this->createHttpRequestFacadeMock()
            ])
            ->setMethods([
                'createHttpRequestLogEntry',
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
     * @return GraphhopperConfig
     */
    protected function createGraphhopperConfig()
    {
        return new GraphhopperConfig();
    }

    /**
     * @return string
     */
    protected function createAddressString() : string
    {
        return sprintf(
            self::ADDRESS_STRING_FORMAT,
            self::ADDRESS_ADDRESS_1,
            self::ADDRESS_ZIP_CODE,
            self::ADDRESS_CITY
        );
    }

    protected function createHttpResponseNoHits() : HttpResponseTransfer
    {
        $httpResponse = new HttpResponseTransfer();
        return $httpResponse
            ->setBody('{"hits": []}')
            ->setCode(200);
    }

    protected function createHttpResponseWithHit() : HttpResponseTransfer
    {
        $httpResponse = new HttpResponseTransfer();
        return $httpResponse
            ->setBody(
                '{"hits": [
                            {
                                "osm_id": 240109189,
                                "osm_type": "N",
                                "country": "Deutschland",
                                "osm_key": "place",
                                "city": "Berlin",
                                "osm_value": "city",
                                "postcode": "10117",
                                "name": "Berlin",
                                "point": {
                                 "lng": 13.3888599,
                                "lat": 52.5170365
                                }
                            }
                        ]}'
            )
            ->setCode(200);
    }
}
