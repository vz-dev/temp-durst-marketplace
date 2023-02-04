<?php
/**
 * Durst - project - Geocoder.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-07
 * Time: 20:08
 */

namespace Pyz\Zed\GoogleApi\Business\Geocoder;



use Generated\Shared\Transfer\GoogleApiCoordinatesTransfer;
use Generated\Shared\Transfer\HttpRequestOptionsTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Orm\Zed\HttpRequest\Persistence\Map\PyzHttpRequestTableMap;
use Pyz\Service\HttpRequest\HttpRequestServiceInterface;
use Pyz\Shared\GoogleApi\GoogleApiConstants;
use Pyz\Zed\GoogleApi\Business\Exception\InvalidRequestException;
use Pyz\Zed\GoogleApi\Business\Exception\LocationNotFoundException;
use Pyz\Zed\GoogleApi\Business\Exception\OverDailyLimitException;
use Pyz\Zed\GoogleApi\Business\Exception\OverQueryLimitException;
use Pyz\Zed\GoogleApi\Business\Exception\RequestDeniedException;
use Pyz\Zed\GoogleApi\Business\Exception\UnknownErrorException;
use Pyz\Zed\GoogleApi\Business\Handler\Json\Request\GoogleApiGeocodingRequestInterface;
use Pyz\Zed\GoogleApi\Business\Handler\Json\Response\GoogleApiGeocodingResponseInterface;
use Pyz\Zed\GoogleApi\Dependency\GoogleApiToHttpRequestBridgeInterface;
use Pyz\Zed\GoogleApi\GoogleApiConfig;


class Geocoder implements GeocoderInterface
{
    /**
     * @var \Pyz\Service\HttpRequest\HttpRequestServiceInterface
     */
    protected $httpRequestService;

    /**
     * @var GoogleApiToHttpRequestBridgeInterface
     */
    protected $httpRequestBridgeFacade;

    /**
     * @var GoogleApiConfig
     */
    protected $config;

    /**
     * Geocoder constructor.
     * @param HttpRequestServiceInterface $httpRequestService
     * @param GoogleApiToHttpRequestBridgeInterface $httpRequestBridgeFacade
     * @param GoogleApiConfig $config
     */
    public function __construct(
        HttpRequestServiceInterface $httpRequestService,
        GoogleApiToHttpRequestBridgeInterface $httpRequestBridgeFacade,
        GoogleApiConfig $config
    )
    {
        $this->httpRequestService = $httpRequestService;
        $this->httpRequestBridgeFacade = $httpRequestBridgeFacade;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $addressString
     * @param string|null $postcode
     * @return GoogleApiCoordinatesTransfer
     * @throws InvalidRequestException
     * @throws LocationNotFoundException
     * @throws OverDailyLimitException
     * @throws OverQueryLimitException
     * @throws RequestDeniedException
     * @throws UnknownErrorException
     */
    public function getCoordinatesForAddressString(
        string $addressString,
        ?string $postcode
    ): GoogleApiCoordinatesTransfer
    {
        $httpRequestTransfer = $this
            ->createHttpRequestTransferForAddress($addressString, $postcode);

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
     * @param string|null $poscode
     * @return HttpRequestTransfer
     */
    protected function createHttpRequestTransferForAddress(string $addressString, ?string $poscode): HttpRequestTransfer
    {
        $httpRequestTransfer = new HttpRequestTransfer();

        $options = $this
            ->createHttpRequestOptionsForAddress($addressString, $poscode);

        $httpRequestTransfer
            ->setOptions($options)
            ->setMethod(PyzHttpRequestTableMap::COL_REQUEST_METHOD_GET)
            ->setUri(GoogleApiConstants::GOOGLE_API_GEOCODING_URL);

        return $httpRequestTransfer;
    }

    /**
     * @param string $addressString
     * @param string|null $postcode
     * @return HttpRequestOptionsTransfer
     */
    protected function createHttpRequestOptionsForAddress(string $addressString, ?string $postcode): HttpRequestOptionsTransfer
    {
        $options = new HttpRequestOptionsTransfer();
        $options
            ->setQuery([
                GoogleApiGeocodingRequestInterface::GEOCODING_ADDRESS_KEY => $addressString,
                GoogleApiGeocodingRequestInterface::GEOCODING_API_KEY => $this->config->getGoogleApiGeocodingKey(),
                GoogleApiGeocodingRequestInterface::GEOCODING_COMPONENTS => $this->getComponentStringWithOrWithoutZipcode($postcode),
            ]);

        return $options;
    }

    /**
     * @param string|null $postcode
     * @return string
     */
    protected function getComponentStringWithOrWithoutZipcode(?string $postcode) : string
    {
        if($postcode !== null){
            return sprintf(
                GoogleApiConstants::GOOGLE_API_GEOCODING_COMPONENTS_WITH_ZIP,
                $postcode
            );
        }

        return GoogleApiConstants::GOOGLE_API_GEOCODING_COMPONENTS;
    }

    /**
     * @param string $addressString
     * @param HttpResponseTransfer $responseTransfer
     * @return GoogleApiCoordinatesTransfer
     * @throws InvalidRequestException
     * @throws LocationNotFoundException
     * @throws OverDailyLimitException
     * @throws OverQueryLimitException
     * @throws RequestDeniedException
     * @throws UnknownErrorException
     */
    protected function getCoordinatesFromResponseForAddress(
        string $addressString,
        HttpResponseTransfer $responseTransfer
    ): GoogleApiCoordinatesTransfer
    {
        $lat = 0;
        $long = 0;

        if ($responseTransfer->getCode() === 200 && $responseTransfer->getErrors()->count() < 1) {

            $coordinates = $this
                ->getCoordinates(
                    $responseTransfer,
                    $addressString
                );

            $lat = $coordinates[GoogleApiGeocodingResponseInterface::RESPONSE_GEOMETRY_LOCATION_LAT];
            $long = $coordinates[GoogleApiGeocodingResponseInterface::RESPONSE_GEOMETRY_LOCATION_LNG];
        }

        $googleApiCoordinatesTransfer = $this->createGoogleApiCoordinatesTransfer();

        return $googleApiCoordinatesTransfer
            ->setLat($lat)
            ->setLng($long)
            ->setQuery($addressString);
    }

    /**
     * @param HttpResponseTransfer $responseTransfer
     * @param string $address
     * @return array
     * @throws InvalidRequestException
     * @throws LocationNotFoundException
     * @throws OverDailyLimitException
     * @throws OverQueryLimitException
     * @throws RequestDeniedException
     * @throws UnknownErrorException
     */
    protected function getCoordinates(
        HttpResponseTransfer $responseTransfer,
        string $address
    ): array
    {
        $result = [
            GoogleApiGeocodingResponseInterface::RESPONSE_GEOMETRY_LOCATION_LAT => 0,
            GoogleApiGeocodingResponseInterface::RESPONSE_GEOMETRY_LOCATION_LNG => 0
        ];

        $response = json_decode($responseTransfer
            ->getBody());
        $results = $response
            ->{GoogleApiGeocodingResponseInterface::RESPONSE_RESULTS};

        if($response->{GoogleApiGeocodingResponseInterface::RESPONSE_STATUS} !== GoogleApiGeocodingResponseInterface::RESPONSE_STATUS_OK)
        {
            switch ($response->{GoogleApiGeocodingResponseInterface::RESPONSE_STATUS})
            {
                case GoogleApiGeocodingResponseInterface::RESPONSE_STATUS_OVER_DAILY_LIMIT:
                    throw new OverDailyLimitException(OverDailyLimitException::MESSAGE);
                    break;
                case GoogleApiGeocodingResponseInterface::RESPONSE_STATUS_OVER_QUERY_LIMIT:
                    throw new OverQueryLimitException(OverQueryLimitException::MESSAGE);
                    break;
                case GoogleApiGeocodingResponseInterface::RESPONSE_STATUS_REQUEST_DENIED:
                    throw new RequestDeniedException(
                        sprintf(
                            RequestDeniedException::MESSAGE,
                            $address
                        )
                    );
                    break;
                case GoogleApiGeocodingResponseInterface::RESPONSE_STATUS_INVALID_REQUEST:
                    throw new InvalidRequestException(
                        sprintf(
                            InvalidRequestException::MESSAGE,
                            $address
                        )
                    );
                    break;
                default:
                    throw new UnknownErrorException(UnknownErrorException::MESSAGE);
                    break;
            }
        }

        if (count($results) === 0) {
            throw new LocationNotFoundException(
                sprintf(
                    LocationNotFoundException::MESSAGE,
                    $address
                )
            );
        }

        if (is_array($results) === true) {
            $bestResult = reset($results);
            $location = $bestResult
                ->{GoogleApiGeocodingResponseInterface::RESPONSE_GEOMETRY}
                ->{GoogleApiGeocodingResponseInterface::RESPONSE_GEOMETRY_LOCATION};

            $result[GoogleApiGeocodingResponseInterface::RESPONSE_GEOMETRY_LOCATION_LAT] = $location
                ->{GoogleApiGeocodingResponseInterface::RESPONSE_GEOMETRY_LOCATION_LAT};

            $result[GoogleApiGeocodingResponseInterface::RESPONSE_GEOMETRY_LOCATION_LNG] = $location
                ->{GoogleApiGeocodingResponseInterface::RESPONSE_GEOMETRY_LOCATION_LNG};
        }

        return $result;
    }

    /**
     * @return GoogleApiCoordinatesTransfer
     */
    protected function createGoogleApiCoordinatesTransfer() : GoogleApiCoordinatesTransfer
    {
        return new GoogleApiCoordinatesTransfer();
    }
}
