<?php
/**
 * Durst - project - Geocoding.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 27.11.19
 * Time: 16:11
 */

namespace Pyz\Zed\Graphhopper\Business\Model;


use Generated\Shared\Transfer\GraphhopperCoordinatesTransfer;
use Generated\Shared\Transfer\HttpRequestOptionsTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Orm\Zed\HttpRequest\Persistence\Map\PyzHttpRequestTableMap;
use Pyz\Service\HttpRequest\HttpRequestServiceInterface;
use Pyz\Shared\Graphhopper\GraphhopperConstants;
use Pyz\Zed\Graphhopper\Business\Exception\AmbiguousLocationException;
use Pyz\Zed\Graphhopper\Business\Exception\LocationNotFoundException;
use Pyz\Zed\Graphhopper\Business\Handler\Json\Request\GeocodingKeyRequestInterface;
use Pyz\Zed\Graphhopper\Business\Handler\Json\Response\GeocodingKeyResponseInterface;
use Pyz\Zed\Graphhopper\Dependency\GraphhopperToHttpRequestBridgeInterface;
use Pyz\Zed\Graphhopper\GraphhopperConfig;

class Geocoding implements GeocodingInterface
{
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
     * Geocoding constructor.
     * @param \Pyz\Service\HttpRequest\HttpRequestServiceInterface $httpRequestService
     * @param \Pyz\Zed\Graphhopper\Dependency\GraphhopperToHttpRequestBridgeInterface $httpRequestBridgeFacade
     * @param \Pyz\Zed\Graphhopper\GraphhopperConfig $config
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
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     * @return \Pyz\Zed\Graphhopper\Business\Model\Coordinates
     * @throws \Pyz\Zed\Graphhopper\Business\Exception\AmbiguousLocationException
     * @throws \Pyz\Zed\Graphhopper\Business\Exception\LocationNotFoundException
     */
    public function getCoordinatesForAddressString(
        string $addressString
    ): GraphhopperCoordinatesTransfer
    {
        $httpRequestTransfer = $this
            ->createHttpRequestTransferForAddress($addressString);

        $httpResponseTransfer = $this
            ->httpRequestService
            ->sendRequest($httpRequestTransfer);

        $this
            ->httpRequestBridgeFacade
            ->createHttpRequestLogEntry(
                $httpRequestTransfer,
                $httpResponseTransfer
            );

        return $this
            ->getCoordinatesFromResponseForAddress(
                $addressString,
                $httpResponseTransfer
            );
    }

    /**
     * @param string $addressString
     * @return HttpRequestTransfer
     */
    protected function createHttpRequestTransferForAddress(string $addressString): HttpRequestTransfer
    {
        $httpRequestTransfer = new HttpRequestTransfer();

        $options = $this
            ->createHttpRequestOptionsForAddress($addressString);

        $httpRequestTransfer
            ->setOptions($options)
            ->setMethod(PyzHttpRequestTableMap::COL_REQUEST_METHOD_GET)
            ->setUri(GraphhopperConstants::URL_GEOCODING);

        return $httpRequestTransfer;
    }

    /**
     * @param string $addressString
     * @return HttpRequestOptionsTransfer
     */
    protected function createHttpRequestOptionsForAddress(string $addressString): HttpRequestOptionsTransfer
    {
        $options = new HttpRequestOptionsTransfer();

        $options
            ->setQuery([
                GeocodingKeyRequestInterface::GEOCODING_LOCALE => $this->config->getGraphhopperLocale(),
                GeocodingKeyRequestInterface::GEOCODING_API_KEY => $this->config->getGraphhopperApiKey(),
                GeocodingKeyRequestInterface::GEOCODING_QUERY => $addressString,
                GeocodingKeyRequestInterface::GEOCODING_RESULT_LIMIT => $this->config->getGeocoderResultLimit(),
                GeocodingKeyRequestInterface::GEOCODING_PROVIDER => $this->config->getGeocoderProvider()
            ]);

        return $options;
    }

    /**
     * @param string $addressString
     * @param HttpResponseTransfer $responseTransfer
     * @return GraphhopperCoordinatesTransfer
     * @throws AmbiguousLocationException
     * @throws LocationNotFoundException
     */
    protected function getCoordinatesFromResponseForAddress(
        string $addressString,
        HttpResponseTransfer $responseTransfer
    ): GraphhopperCoordinatesTransfer
    {
        $lat = 0;
        $long = 0;

        if ($responseTransfer->getCode() === 200 && $responseTransfer->getErrors()->count() < 1) {

            $coordinates = $this
                ->getCoordinates(
                    $responseTransfer,
                    $addressString
                );

            $lat = $coordinates[GeocodingKeyResponseInterface::RESPONSE_LAT];
            $long = $coordinates[GeocodingKeyResponseInterface::RESPONSE_LNG];
        }

        $graphhopperCoordinateTransfer = $this->createGraphhopperCoordinatesTransfer();

        return $graphhopperCoordinateTransfer
            ->setLat($lat)
            ->setLng($long)
            ->setQuery($addressString);
    }

    /**
     * @param \Generated\Shared\Transfer\HttpResponseTransfer $responseTransfer
     * @param string $address
     * @return array
     * @throws \Pyz\Zed\Graphhopper\Business\Exception\AmbiguousLocationException
     * @throws \Pyz\Zed\Graphhopper\Business\Exception\LocationNotFoundException
     */
    protected function getCoordinates(
        HttpResponseTransfer $responseTransfer,
        string $address
    ): array
    {
        $result = [
            GeocodingKeyResponseInterface::RESPONSE_LAT => 0,
            GeocodingKeyResponseInterface::RESPONSE_LNG => 0
        ];

        $response = json_decode($responseTransfer
            ->getBody());
        $hits = $response
            ->{GeocodingKeyResponseInterface::RESPONSE_HITS};

        if (count($hits) === 0) {
            throw new LocationNotFoundException(
                sprintf(
                    LocationNotFoundException::MESSAGE,
                    $address
                )
            );
        }

        if (is_array($hits) === true) {
            $bestResult = reset($hits);
            $point = $bestResult
                ->{GeocodingKeyResponseInterface::RESPONSE_POINT};

            $result[GeocodingKeyResponseInterface::RESPONSE_LAT] = $point
                ->{GeocodingKeyResponseInterface::RESPONSE_LAT};

            $result[GeocodingKeyResponseInterface::RESPONSE_LNG] = $point
                ->{GeocodingKeyResponseInterface::RESPONSE_LNG};
        }

        return $result;
    }

    /**
     * @return GraphhopperCoordinatesTransfer
     */
    protected function createGraphhopperCoordinatesTransfer() : GraphhopperCoordinatesTransfer
    {
        return new GraphhopperCoordinatesTransfer();
    }
}
