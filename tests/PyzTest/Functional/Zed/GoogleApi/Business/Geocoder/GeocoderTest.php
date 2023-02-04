<?php
/**
 * Durst - project - GeocoderTest.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-11
 * Time: 05:08
 */

namespace PyzTest\Functional\Zed\GoogleApi\Business\Geocoder;


use Codeception\Test\Unit;
use Generated\Shared\Transfer\HttpResponseTransfer;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Service\HttpRequest\HttpRequestService;
use Pyz\Zed\GoogleApi\Business\Exception\InvalidRequestException;
use Pyz\Zed\GoogleApi\Business\Exception\LocationNotFoundException;
use Pyz\Zed\GoogleApi\Business\Exception\OverDailyLimitException;
use Pyz\Zed\GoogleApi\Business\Exception\OverQueryLimitException;
use Pyz\Zed\GoogleApi\Business\Exception\RequestDeniedException;
use Pyz\Zed\GoogleApi\Business\Exception\UnknownErrorException;
use Pyz\Zed\GoogleApi\Business\Geocoder\Geocoder;
use Pyz\Zed\GoogleApi\Business\Geocoder\GeocoderInterface;
use Pyz\Zed\GoogleApi\Dependency\GoogleApiToHttpRequestBridge;
use Pyz\Zed\GoogleApi\GoogleApiConfig;
use Pyz\Zed\HttpRequest\Business\HttpRequestFacade;

class GeocoderTest extends Unit
{
    public const ADDRESS_STRING_FORMAT = '%s %s %s';
    public const ADDRESS_ADDRESS_1 = 'Oskar Jäger Str 173';
    public const ADDRESS_CITY = 'Köln';
    public const ADDRESS_ZIP_CODE = '50825';

    public const ADDRESS_EXPECTED_LAT = '50.9478287';
    public const ADDRESS_EXPECTED_LNG = '6.905976099999999';

    public const ADDRESS_FALSE_TEST_LOCATION = 'NOWHERE-INTERESTING';

    /**
     * @var GeocoderInterface
     */
    protected $geocoder;

    /**
     * @var HttpRequestService|MockObject
     */
    protected $httpRequestService;

    /**
     * @var GoogleApiToHttpRequestBridge|MockObject
     */
    protected $httpRequestBridgeFacade;

    /**
     * @var GoogleApiConfig|MockObject
     */
    protected $config;

    protected function _before()
    {
        $this->httpRequestService = $this->createHttpRequestServiceMock();
        $this->httpRequestBridgeFacade = $this->createHttpRequestBridgeFacadeMock();
        $this->config = $this->createGoogleApiConfig();

        $this->geocoder = new Geocoder(
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
        $googleApiCoordinatesTransfer = $this->geocoder->getCoordinatesForAddressString($this->createAddressString(), null);

        $this->assertNotNull(
            $googleApiCoordinatesTransfer->getLat()
        );

        $this->assertNotNull(
            $googleApiCoordinatesTransfer->getLat()
        );

        $this->assertNotNull(
            $googleApiCoordinatesTransfer->getQuery()
        );
    }

    public function testGeocoderHydratesCorrectCoordinates()
    {
        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->willReturn($this->createHttpResponseWithHit());

        $googleApiCoordinatesTransfer = $this->geocoder->getCoordinatesForAddressString($this->createAddressString(), null);

        $this->assertEquals(
            self::ADDRESS_EXPECTED_LAT,
            $googleApiCoordinatesTransfer->getLat()
        );

        $this->assertEquals(
            self::ADDRESS_EXPECTED_LNG,
            $googleApiCoordinatesTransfer->getLng()
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

        $this->geocoder->getCoordinatesForAddressString(self::ADDRESS_FALSE_TEST_LOCATION, null);
    }

    public function testGeocoderOverDailyLimitException()
    {
        $this->expectException(
            OverDailyLimitException::class
        );

        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->willReturn($this->createHttpResponseOverDailyLimit());

        $this->geocoder->getCoordinatesForAddressString($this->createAddressString(), null);
    }

    public function testGeocoderOverQueryLimitException()
    {
        $this->expectException(
            OverQueryLimitException::class
        );

        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->willReturn($this->createHttpResponseOverQueryLimit());

        $this->geocoder->getCoordinatesForAddressString($this->createAddressString(), null);
    }

    public function testGeocoderRequestDeniedException()
    {
        $this->expectException(
            RequestDeniedException::class
        );

        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->willReturn($this->createHttpResponseRequestDenied());

        $this->geocoder->getCoordinatesForAddressString($this->createAddressString(), null);
    }

    public function testGeocoderInvalidRequestException()
    {
        $this->expectException(
            InvalidRequestException::class
        );

        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->willReturn($this->createHttpResponseInvalidRequest());

        $this->geocoder->getCoordinatesForAddressString($this->createAddressString(), null);
    }

    public function testGeocoderUnknownErrorException()
    {
        $this->expectException(
            UnknownErrorException::class
        );

        $this
            ->httpRequestService
            ->expects($this->atLeastOnce())
            ->method('sendRequest')
            ->willReturn($this->createHttpResponseUnknownError());

        $this->geocoder->getCoordinatesForAddressString($this->createAddressString(), null);
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
     * @return MockObject|GoogleApiToHttpRequestBridge
     */
    protected function createHttpRequestBridgeFacadeMock()
    {
        return $this
            ->getMockBuilder(GoogleApiToHttpRequestBridge::class)
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
     * @return GoogleApiConfig
     */
    protected function createGoogleApiConfig()
    {
        return new GoogleApiConfig();
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
            ->setBody('{"results": [], "status": "OK"}')
            ->setCode(200);
    }

    protected function createHttpResponseOverDailyLimit() : HttpResponseTransfer
    {
        $httpResponse = new HttpResponseTransfer();
        return $httpResponse
            ->setBody('{"results": [], "status": "OVER_DAILY_LIMIT"}')
            ->setCode(200);
    }

    protected function createHttpResponseOverQueryLimit() : HttpResponseTransfer
    {
        $httpResponse = new HttpResponseTransfer();
        return $httpResponse
            ->setBody('{"results": [], "status": "OVER_QUERY_LIMIT"}')
            ->setCode(200);
    }

    protected function createHttpResponseRequestDenied() : HttpResponseTransfer
    {
        $httpResponse = new HttpResponseTransfer();
        return $httpResponse
            ->setBody('{"results": [], "status": "REQUEST_DENIED"}')
            ->setCode(200);
    }

    protected function createHttpResponseInvalidRequest() : HttpResponseTransfer
    {
        $httpResponse = new HttpResponseTransfer();
        return $httpResponse
            ->setBody('{"results": [], "status": "INVALID_REQUEST"}')
            ->setCode(200);
    }

    protected function createHttpResponseUnknownError() : HttpResponseTransfer
    {
        $httpResponse = new HttpResponseTransfer();
        return $httpResponse
            ->setBody('{"results": [], "status": "UNKNOWN_ERROR"}')
            ->setCode(200);
    }

    protected function createHttpResponseWithHit() : HttpResponseTransfer
    {
        $httpResponse = new HttpResponseTransfer();
        return $httpResponse
            ->setBody(
                '{
                          "results": [
                            {
                              "address_components": [
                                {
                                  "long_name": "173",
                                  "short_name": "173",
                                  "types": [
                                    "street_number"
                                  ]
                                },
                                {
                                  "long_name": "Oskar-Jäger-Straße",
                                  "short_name": "Oskar-Jäger-Straße",
                                  "types": [
                                    "route"
                                  ]
                                },
                                {
                                  "long_name": "Ehrenfeld",
                                  "short_name": "Ehrenfeld",
                                  "types": [
                                    "political",
                                    "sublocality",
                                    "sublocality_level_1"
                                  ]
                                },
                                {
                                  "long_name": "Köln",
                                  "short_name": "Köln",
                                  "types": [
                                    "locality",
                                    "political"
                                  ]
                                },
                                {
                                  "long_name": "Köln",
                                  "short_name": "K",
                                  "types": [
                                    "administrative_area_level_2",
                                    "political"
                                  ]
                                },
                                {
                                  "long_name": "Nordrhein-Westfalen",
                                  "short_name": "NRW",
                                  "types": [
                                    "administrative_area_level_1",
                                    "political"
                                  ]
                                },
                                {
                                  "long_name": "Germany",
                                  "short_name": "DE",
                                  "types": [
                                    "country",
                                    "political"
                                  ]
                                },
                                {
                                  "long_name": "50825",
                                  "short_name": "50825",
                                  "types": [
                                    "postal_code"
                                  ]
                                }
                              ],
                              "formatted_address": "Oskar-Jäger-Straße 173, 50825 Köln, Germany",
                              "geometry": {
                                "location": {
                                  "lat": 50.9478287,
                                  "lng": 6.905976099999999
                                },
                                "location_type": "ROOFTOP",
                                "viewport": {
                                  "northeast": {
                                    "lat": 50.94917768029149,
                                    "lng": 6.907325080291502
                                  },
                                  "southwest": {
                                    "lat": 50.9464797197085,
                                    "lng": 6.904627119708497
                                  }
                                }
                              },
                              "place_id": "ChIJG_9UZDolv0cRJn-ukStf6-A",
                              "plus_code": {
                                "compound_code": "WWX4+49 Cologne, Germany",
                                "global_code": "9F28WWX4+49"
                              },
                              "types": [
                                "street_address"
                              ]
                            }
                          ],
                          "status": "OK"
                        }'
            )
            ->setCode(200);
    }
}
