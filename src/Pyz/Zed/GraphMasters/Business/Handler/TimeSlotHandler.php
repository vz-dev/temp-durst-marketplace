<?php
/**
 * Durst - project - TimeSlotHandler.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 22.06.21
 * Time: 22:22
 */

namespace Pyz\Zed\GraphMasters\Business\Handler;


use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\GraphMastersApiTimeSlotResponseTransfer;
use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Generated\Shared\Transfer\HttpRequestOptionsTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\HttpRequest\Persistence\Map\PyzHttpRequestTableMap;
use Pyz\Service\HttpRequest\HttpRequestServiceInterface;
use Pyz\Zed\GraphMasters\Business\Handler\Json\Request\EvaluateTimeSlotsKeyRequestInterface as Request;
use Pyz\Zed\GraphMasters\Business\Handler\Json\Response\EvaluateTimeSlotsKeyResponseInterface as Response;
use Pyz\Zed\GraphMasters\Business\Model\GraphMastersSettingsInterface;
use Pyz\Zed\GraphMasters\GraphMastersConfig;
use Pyz\Zed\HttpRequest\Business\HttpRequestFacadeInterface;

class TimeSlotHandler implements TimeSlotHandlerInterface
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
     * @var GraphMastersSettingsTransfer
     */
    protected $branchSettings;

    /**
     * OrderImporter constructor.
     * @param GraphMastersSettingsInterface $settings
     * @param HttpRequestFacadeInterface $httpRequestFacade
     * @param HttpRequestServiceInterface $httpRequestService
     * @param GraphMastersConfig $graphMastersConfig
     */
    public function __construct(
        GraphMastersSettingsInterface $settings,
        HttpRequestFacadeInterface $httpRequestFacade,
        HttpRequestServiceInterface $httpRequestService,
        GraphMastersConfig $graphMastersConfig
    )
    {
        $this->settings = $settings;
        $this->httpRequestFacade = $httpRequestFacade;
        $this->httpRequestService = $httpRequestService;
        $this->config = $graphMastersConfig;
    }

    /**
     * @param AppApiRequestTransfer $appApiRequestTransfer
     * @return AppApiResponseTransfer
     */
    public function evaluateTimeSlot(AppApiRequestTransfer $appApiRequestTransfer) : AppApiResponseTransfer
    {
        $this->branchSettings = $this->settings->getSettingsByIdBranch($appApiRequestTransfer->getIdBranch());

        $httpRequest = $this
            ->createRequestTransfer($appApiRequestTransfer);

        $httpResponse = $this
            ->httpRequestService
            ->sendRequest($httpRequest);

        $this
            ->httpRequestFacade
            ->createHttpRequestLogEntry($httpRequest, $httpResponse);

        return $this
            ->createAppApiResponseTransfer($httpResponse);

    }

    /**
     * @param AppApiRequestTransfer $appApiRequestTransfer
     * @return HttpRequestTransfer
     */
    protected function createRequestTransfer(AppApiRequestTransfer $appApiRequestTransfer) : HttpRequestTransfer
    {
        $httpRequestTransfer = new HttpRequestTransfer();

        $httpRequestTransfer
            ->setOptions($this->createRequestOptions($appApiRequestTransfer))
            ->setMethod(PyzHttpRequestTableMap::COL_REQUEST_METHOD_POST)
            ->setHeaders(['Authorization' => 'api-key '.$this->config->getGraphMastersApiKey(),])
            ->setUri($this->config->getApiEndpointEvaluateTimeSlots());

        return $httpRequestTransfer;
    }

    /**
     * @param AppApiRequestTransfer $quoteTransfer
     * @return HttpRequestOptionsTransfer
     */
    protected function createRequestOptions(AppApiRequestTransfer $appApiRequestTransfer) : HttpRequestOptionsTransfer
    {

        $httpRequestOptionsTransfer = new HttpRequestOptionsTransfer();

        $requestBody = [];
        $requestBody[Request::IMPORT_ORDER_KEY] = [];
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_ID] = null;
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_DEPOT_ID] = $this->branchSettings->getDepotApiId();
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_STATUS] = 'open';
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_CUSTOMER_UUID] = null;

        $addressParts = $this->splitStreetAndHouseNo($appApiRequestTransfer->getShippingAddress()->getAddress1());

        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_ADDRESS] = [];
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_ADDRESS][Request::IMPORT_ORDER_KEY_ADDRESS_STREET] = trim($addressParts[1]);
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_ADDRESS][Request::IMPORT_ORDER_KEY_ADDRESS_HOUSE_NO] = trim($addressParts[2]);
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_ADDRESS][Request::IMPORT_ORDER_KEY_ADDRESS_ZIP_CODE] = $appApiRequestTransfer->getShippingAddress()->getZipCode();
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_ADDRESS][Request::IMPORT_ORDER_KEY_ADDRESS_CITY] = $appApiRequestTransfer->getShippingAddress()->getCity();
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_ADDRESS][Request::IMPORT_ORDER_KEY_ADDRESS_COUNTRY] = 'Germany';

        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_GEOLOCATION] = [];
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_GEOLOCATION][Request::IMPORT_ORDER_KEY_GEOLOCATION_LAT] = $appApiRequestTransfer->getShippingAddress()->getLat();
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_GEOLOCATION][Request::IMPORT_ORDER_KEY_GEOLOCATION_LNG] = $appApiRequestTransfer->getShippingAddress()->getLng();

        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_DATE_OF_DELIVERY] = null;

        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_TIMESLOT] = [];
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_TIMESLOT][Request::IMPORT_ORDER_KEY_TIMESLOT_START_TIME] = null;
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_TIMESLOT][Request::IMPORT_ORDER_KEY_TIMESLOT_END_TIME] = null;

        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_STOP_TIME_MINUTES] = null;

        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD] = [];
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD_COUNT] = $appApiRequestTransfer->getRequestedProductsAmount();
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD_WEIGHT] = ($appApiRequestTransfer->getRequestedProductsWeight() / 1000);
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD][Request::IMPORT_ORDER_KEY_SHIPMENT_LOAD_VOLUME] = null;

        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_RECIPIENT] ='';
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_SENDER] = 'todo';
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_LABEL] =$this->getLabelFromAddress($appApiRequestTransfer->getShippingAddress());
        $requestBody[Request::IMPORT_ORDER_KEY][Request::IMPORT_ORDER_KEY_SHIPMENT][Request::IMPORT_ORDER_KEY_SHIPMENT_BARCODE] = null;

        $requestBody[Request::EVALUTE_TIME_SLOTS_KEY] = [];

        foreach($appApiRequestTransfer->getTimeSlots() as $slot){
            $requestBody[Request::EVALUTE_TIME_SLOTS_KEY][] = [
                Request::EVALUTE_TIME_SLOTS_KEY_START_TIME => str_replace('+00:00', '+01:00', $slot['start_time']),
                Request::EVALUTE_TIME_SLOTS_KEY_END_TIME => str_replace('+00:00', '+01:00', $slot['end_time']),
                Request::EVALUTE_TIME_SLOTS_KEY_IMPORTANCE => 1.0,
                Request::EVALUTE_TIME_SLOTS_KEY_REASON => 'small'
            ];
        }

        $this->addMediumAndLargeTimeslots($requestBody[Request::EVALUTE_TIME_SLOTS_KEY]);

        $httpRequestOptionsTransfer
            ->setJson($requestBody)
            ->setHeaders([
                'Content-Type' => 'application/json'
            ]);

        return $httpRequestOptionsTransfer;
    }

    /**
     * @param string $address
     * @return array
     */
    protected function splitStreetAndHouseNo(string $address) : array
    {
        $result = [
            1 => $address,
            2 => ''
        ];

        if ( preg_match('/([^\d]+)\s?(.+)/i', $address, $matches) )
        {
            return $matches;
        }

        return $result;
    }


    /**
     * @param QuoteTransfer $quoteTransfer
     * @return int
     */
    protected function getItemCount(QuoteTransfer $quoteTransfer) : int
    {
        $itemCount = 0;

        foreach($quoteTransfer->getItems() as $item){
            $itemCount += $item->getQuantity();
        }

        return $itemCount;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return float
     */
    protected function getWeight(QuoteTransfer $quoteTransfer) : float
    {
        $weight = 0;

        foreach($quoteTransfer->getItems() as $item){
            $weight += $item->getDeposit()->getWeight() * $item->getQuantity();
        }

        return ($weight / 1000);
    }

    /**
     * @param AddressTransfer $addressTransfer
     * @return string
     */
    protected function getLabelFromAddress(AddressTransfer $addressTransfer)
    {
        return sprintf(
            '%s, %s %s, Germany',
            $addressTransfer->getAddress1(),
            $addressTransfer->getZipCode(),
            $addressTransfer->getCity()
        );
    }

    protected function createAppApiResponseTransfer(HttpResponseTransfer $httpResponseTransfer) : AppApiResponseTransfer
    {
        $appApiResponseTranfer = (new AppApiResponseTransfer());

        // todo proper error handling
        if($httpResponseTransfer->getCode() !== 200)
        {
            $appApiResponseTranfer->setError((new ErrorTransfer())->setCode('55555')->setMessage('Ipsum'));

            return $appApiResponseTranfer;
        }

        $data = json_decode($httpResponseTransfer
            ->getBody());

        foreach($data->{Response::EVALUTE_TIME_SLOTS_KEY_EVALUATION_RESULTS} as $timeSlot)
        {

            $timeSlotResponse = (new GraphMastersApiTimeSlotResponseTransfer())
                ->setStartTime($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_TIME_SLOT}->{Response::EVALUTE_TIME_SLOTS_KEY_START_TIME})
                ->setEndTime($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_TIME_SLOT}->{Response::EVALUTE_TIME_SLOTS_KEY_END_TIME})
                ->setImportance($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_TIME_SLOT}->{Response::EVALUTE_TIME_SLOTS_KEY_IMPORTANCE})
                ->setReason($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_TIME_SLOT}->{Response::EVALUTE_TIME_SLOTS_KEY_REASON})
                ->setEvaluationSucceeded($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_EVALUATION_SUCCEEDED})
                ->setNumberOfActualOrders($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_NUM_OF_ORDERS})
                ->setNumberOfPredictedOrders($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_NUM_OF_PREDICTED_ORDERS})
                ->setNumberOfUnperformedOrders($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_NUM_UNPERFROMED_ORDERS})
                ->setTimeSlotPossible($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_TIME_SLOT_POSSIBLE})
                ->setCostInExtraDrivingMinutes($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_EXTRA_COST_DRIVING})
                ->setExtraWorkTimeMinutes($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_EXTRA_WORK_TIME_MINUTES})
                ->setExtraDistanceKilometer($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_EXTRA_DISTANCE_KILOMETER})
                ->setEta($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_ETA})
                ->setError($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_ERROR});

            if(isset($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_TOUR_ID})){
                $timeSlotResponse
                    ->setTourId($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_TOUR_ID});
            }

            if(isset($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_DRIVER_ID})){
                $timeSlotResponse
                    ->setDriverId($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_DRIVER_ID});
            }

            if(isset($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_VEHICLE_ID})){
                $timeSlotResponse
                    ->setVehicleId($timeSlot->{Response::EVALUTE_TIME_SLOTS_KEY_VEHICLE_ID});
            }

            $appApiResponseTranfer->addGraphMastersEvaluatedTimeSlots($timeSlotResponse);
        }

        return $appApiResponseTranfer;
    }

    /**
     * @param array $timeSlots
     */
    protected function addMediumAndLargeTimeslots(array &$timeSlots)
    {
        $date = (new DateTime())->setTimezone(new DateTimeZone('Europe/Berlin'));
        for ($i = 0; $i <= $this->config->getDaysInAdvance(); ++$i) {

            $weekday = $date->format('l');

            $soltsPerWeekday =  $this->getOpeningTimesPerWeekDay($weekday);

            if($soltsPerWeekday > 0){
                $times = $this->getEarliestAndLatestForWeekday($weekday);
                $start = $this->getHoursAndMins($times['min']);
                $end = $this->getHoursAndMins($times['max']);

                $timeSlots[] = [
                    Request::EVALUTE_TIME_SLOTS_KEY_START_TIME => $date->setTime($start[0],$start[1])->format(DATE_ATOM),
                    Request::EVALUTE_TIME_SLOTS_KEY_END_TIME => $date->setTime($end[0],$end[1])->format(DATE_ATOM),
                    Request::EVALUTE_TIME_SLOTS_KEY_IMPORTANCE => 1.0,
                    Request::EVALUTE_TIME_SLOTS_KEY_REASON => 'large'
                ];
            }

            if($soltsPerWeekday > 1){
                $this->addMediumSlots($timeSlots, $date);
            }

            $date->modify('+1 day');
        }
    }

    /**
     * @param array $timeSlots
     * @param DateTime $date
     */
    protected function addMediumSlots(array &$timeSlots, DateTime $date)
    {
        $settings = $this->branchSettings;
        $weekday = strtolower(date('l', strtotime($date->format('Y-m-d'))));

        $earliestDeliveryPossible = $this->getEarliestOpeningAfterCommisioning();

        if($this->getOpeningTimesPerWeekDay($weekday) > 1){
            foreach($settings->getOpeningTimes() as $openingTime){
                if($openingTime->getWeekday() === $weekday && ($openingTime->getStartTime() !== null && $openingTime->getEndTime() !== null)){

                    $start = $this->getHoursAndMins($openingTime->getStartTime());
                    $end = $this->getHoursAndMins($openingTime->getEndTime());

                    if($earliestDeliveryPossible->getTimestamp() <= $date->setTime($start[0], $start[1])->getTimestamp())
                    {
                        $timeSlots[] = [
                            Request::EVALUTE_TIME_SLOTS_KEY_START_TIME => $date->setTime($start[0], $start[1])->format(DATE_ATOM),
                            Request::EVALUTE_TIME_SLOTS_KEY_END_TIME => $date->setTime($end[0], $end[1])->format(DATE_ATOM),
                            Request::EVALUTE_TIME_SLOTS_KEY_IMPORTANCE => 1.0,
                            Request::EVALUTE_TIME_SLOTS_KEY_REASON => 'medium'
                        ];
                    }
                }
            }
        }
    }

    protected function getEarliestAndLatestForWeekday(string $weekday) : array
    {
        $settings = $this->branchSettings;
        $dayTimes = [];

        foreach($settings->getOpeningTimes() as $openingTime) {
            if($openingTime->getWeekday() === strtolower($weekday))
            {
                if(!isset($dayTimes['min'])){
                    $dayTimes['min'] = $openingTime->getStartTime();
                } elseif ($openingTime->getStartTime() < $dayTimes['min']){
                    $dayTimes['min'] = $openingTime->getStartTime();
                }

                if(!isset($dayTimes['max'])){
                    $dayTimes['max'] = $openingTime->getEndTime();
                } elseif ($dayTimes['max'] < $openingTime->getEndTime()){
                    $dayTimes['max'] = $openingTime->getEndTime();
                }
            }
        }

        return $dayTimes;
    }

    /**
     * @param string $weekday
     * @return int
     */
    protected function getOpeningTimesPerWeekDay(string $weekday) : int
    {
        $openingCount = 0;
        $settings = $this->branchSettings;

        foreach($settings->getOpeningTimes() as $openingTime) {
            if($openingTime->getWeekday() === strtolower($weekday))
            {
                $openingCount++;
            }
        }

        return $openingCount;
    }
    /**
     * @param string $hourMin
     * @return array
     */
    protected function getHoursAndMins(string $hourMin) : array
    {
        return explode(":", $hourMin);
    }

    /**
     * @return DateTime
     */
    protected function getEarliestOpeningAfterCommisioning() : DateTime
    {
        $now = (new DateTime())->setTimezone(new DateTimeZone('Europe/Berlin'));
        $day = $now->format('w');

        $nextCommissioning = null;
        $diffTime = 86400 * $this->config->getDaysInAdvance();

        foreach ($this->branchSettings->getCommissioningTimes() as $commissioning_time)
        {
            list($hours, $minutes, $seconds) = explode(':', $commissioning_time->getStartTime());
            $commisionSlotDateTime = (clone $now)->setTime($hours, $minutes);

            if($this->getNumericDayOfWeek($commissioning_time->getWeekday()) !== $day)
            {
                $commisionSlotDateTime->modify(sprintf('next %s', $commissioning_time->getWeekday()));
            }

            $currentDiff = $commisionSlotDateTime->format('U') - $now->format('U');


            if(($currentDiff < $diffTime && $currentDiff >= 0))
            {
                $diffTime = $currentDiff;
                list($hours, $minutes, $seconds) = explode(':', $commissioning_time->getEndTime());
                $nextCommissioning = $commisionSlotDateTime->setTime($hours, $minutes);
            }
        }

        $nextOpening= null;
        $diffTime = 86400 * $this->config->getDaysInAdvance();

        $day = $nextCommissioning->format('w');

        foreach ($this->branchSettings->getOpeningTimes() as $opening_time)
        {
            list($hours, $minutes, $seconds) = explode(':', $opening_time->getStartTime());
            $openingSlotDateTime = (clone $nextCommissioning)->setTime($hours, $minutes);

            if($this->getNumericDayOfWeek($opening_time->getWeekday()) !== $day)
            {
                $openingSlotDateTime->modify(sprintf('next %s', $opening_time->getWeekday()));
            }

            $currentDiff = $openingSlotDateTime->format('U') - $nextCommissioning->format('U');

            if(($currentDiff < $diffTime && $currentDiff >= 0))
            {
                $diffTime = $currentDiff;
                $nextOpening = $openingSlotDateTime;
            }
        }

        return $nextOpening;
    }

    /**
     * @param string $weekDay
     * @return string
     */
    protected function getNumericDayOfWeek(string $weekDay): string
    {
        $days = ['sunday' => 0, 'monday' => 1,'tuesday' => 2,'wednesday' => 3,'thursday' => 4,'friday' => 5,'saturday' => 6];

        return (string) $days[$weekDay];
    }
}
