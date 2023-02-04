<?php

namespace Pyz\Zed\GraphMasters\Business\Handler;

use Generated\Shared\Transfer\GraphMastersApiOrderUpdateTransfer;
use Generated\Shared\Transfer\HttpRequestErrorTransfer;
use Generated\Shared\Transfer\HttpRequestOptionsTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Orm\Zed\HttpRequest\Persistence\Map\PyzHttpRequestTableMap;
use Pyz\Service\HttpRequest\HttpRequestServiceInterface;
use Pyz\Zed\GraphMasters\Business\Exception\BadResponseException;
use Pyz\Zed\GraphMasters\Business\Exception\FailedRequestException;
use Pyz\Zed\GraphMasters\Business\Handler\Json\Request\ImportOrderKeyRequestInterface as Request;
use Pyz\Zed\GraphMasters\Business\Model\GraphMastersSettingsInterface;
use Pyz\Zed\GraphMasters\GraphMastersConfig;
use Pyz\Zed\HttpRequest\Business\HttpRequestFacadeInterface;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class OrderHandler implements OrderHandlerInterface
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
     * @param GraphMastersApiOrderUpdateTransfer $orderUpdateTransfer
     */
    public function importOrder(GraphMastersApiOrderUpdateTransfer $orderUpdateTransfer): void
    {
        $httpRequest = $this->createHttpRequest($orderUpdateTransfer, $this->config->getApiEndpointImportOrder());

        $httpResponse = $this
            ->httpRequestService
            ->sendRequest($httpRequest);

        $this
            ->httpRequestFacade
            ->createHttpRequestLogEntry($httpRequest, $httpResponse);

        $this->checkResponse($httpResponse);
    }

    /**
     * @param GraphMastersApiOrderUpdateTransfer $orderUpdateTransfer
     * @param string $uri
     * @return HttpRequestTransfer
     */
    protected function createHttpRequest(
        GraphMastersApiOrderUpdateTransfer $orderUpdateTransfer,
        string $uri
    ): HttpRequestTransfer {
        $httpRequest = new HttpRequestTransfer();

        $httpRequest
            ->setOptions($this->createHttpRequestOptions($orderUpdateTransfer))
            ->setMethod(PyzHttpRequestTableMap::COL_REQUEST_METHOD_POST)
            ->setHeaders(['Authorization' => 'api-key '.$this->config->getGraphMastersApiKey()])
            ->setUri($uri);

        return $httpRequest;
    }

    /**
     * @param GraphMastersApiOrderUpdateTransfer $orderUpdateTransfer
     *
     * @return HttpRequestOptionsTransfer
     */
    protected function createHttpRequestOptions(
        GraphMastersApiOrderUpdateTransfer $orderUpdateTransfer
    ): HttpRequestOptionsTransfer {
        $httpRequestOptions = new HttpRequestOptionsTransfer();

        $requestBody = [];
        $requestBody[Request::IMPORT_ORDER_KEY_ID] = $orderUpdateTransfer->getId();
        $requestBody[Request::IMPORT_ORDER_KEY_DEPOT_ID] = $orderUpdateTransfer->getDepotId();
        $requestBody[Request::IMPORT_ORDER_KEY_STATUS] = $orderUpdateTransfer->getStatus();
        $requestBody[Request::IMPORT_ORDER_KEY_CUSTOMER_UUID] = $orderUpdateTransfer->getCustomerUuid();

        if ($orderUpdateTransfer->getAddress() !== null) {
            $addressTransfer = $orderUpdateTransfer->getAddress();

            $requestBody[Request::IMPORT_ORDER_KEY_ADDRESS] = [];
            $requestBody[Request::IMPORT_ORDER_KEY_ADDRESS][Request::IMPORT_ORDER_KEY_ADDRESS_STREET] = $addressTransfer->getStreet();
            $requestBody[Request::IMPORT_ORDER_KEY_ADDRESS][Request::IMPORT_ORDER_KEY_ADDRESS_HOUSE_NO] = $addressTransfer->getHouseNumber();
            $requestBody[Request::IMPORT_ORDER_KEY_ADDRESS][Request::IMPORT_ORDER_KEY_ADDRESS_ZIP_CODE] = $addressTransfer->getZipCode();
            $requestBody[Request::IMPORT_ORDER_KEY_ADDRESS][Request::IMPORT_ORDER_KEY_ADDRESS_CITY] = $addressTransfer->getCity();
            $requestBody[Request::IMPORT_ORDER_KEY_ADDRESS][Request::IMPORT_ORDER_KEY_ADDRESS_COUNTRY] = $addressTransfer->getCountry();
        }

        if ($orderUpdateTransfer->getGeoLocation() !== null) {
            $geoLocationTransfer = $orderUpdateTransfer->getGeoLocation();

            $requestBody[Request::IMPORT_ORDER_KEY_GEOLOCATION] = [];
            $requestBody[Request::IMPORT_ORDER_KEY_GEOLOCATION][Request::IMPORT_ORDER_KEY_GEOLOCATION_LAT] = $geoLocationTransfer->getLat();
            $requestBody[Request::IMPORT_ORDER_KEY_GEOLOCATION][Request::IMPORT_ORDER_KEY_GEOLOCATION_LNG] = $geoLocationTransfer->getLng();
        }

        $requestBody[Request::IMPORT_ORDER_KEY_DATE_OF_DELIVERY] = $orderUpdateTransfer->getDateOfDelivery();

        if ($orderUpdateTransfer->getTimeSlot() !== null) {
            $timeSlotTransfer = $orderUpdateTransfer->getTimeSlot();

            $requestBody[Request::IMPORT_ORDER_KEY_TIMESLOT] = [];
            $requestBody[Request::IMPORT_ORDER_KEY_TIMESLOT][Request::IMPORT_ORDER_KEY_TIMESLOT_START_TIME] = $timeSlotTransfer->getStartTime();
            $requestBody[Request::IMPORT_ORDER_KEY_TIMESLOT][Request::IMPORT_ORDER_KEY_TIMESLOT_END_TIME] = $timeSlotTransfer->getEndTime();
        }

        $requestBody[Request::IMPORT_ORDER_KEY_STOP_TIME_MINUTES] = $orderUpdateTransfer->getStopTimeMinutes();

        if ($orderUpdateTransfer->getShipment() !== null) {
            $shipmentTransfer = $orderUpdateTransfer->getShipment();

            $requestBody[Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_RECIPIENT] = $shipmentTransfer->getRecipient();
            $requestBody[Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_SENDER] = $shipmentTransfer->getSender();
            $requestBody[Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_LABEL] = $shipmentTransfer->getLabel();
            $requestBody[Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_BARCODE] = $shipmentTransfer->getBarcode();

            $loadTransfer = $shipmentTransfer->getLoad();

            if ($loadTransfer !== null) {
                $requestBody[Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD] = [];
                $requestBody[Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD_COUNT] = $loadTransfer->getItemCount();
                $requestBody[Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD_WEIGHT] = $loadTransfer->getWeightKilogram();
                $requestBody[Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD_VOLUME] = $loadTransfer->getVolumeCubicMeter();
            }
        }

        $this->removeElementsWithNullValue($requestBody);

        $httpRequestOptions
            ->setJson($requestBody)
            ->setHeaders([
                'Content-Type' => 'application/json'
            ]);

        return $httpRequestOptions;
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

    /**
     * @param array $array
     * @return array
     */
    protected function removeElementsWithNullValue(array &$array): array
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->removeElementsWithNullValue($value);
            }

            if ($value === null) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
