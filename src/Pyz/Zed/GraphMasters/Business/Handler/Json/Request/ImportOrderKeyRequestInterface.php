<?php
/**
 * Durst - project - ImportOrderKeyRequestInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 25.05.21
 * Time: 23:23
 */

namespace Pyz\Zed\GraphMasters\Business\Handler\Json\Request;


class ImportOrderKeyRequestInterface
{
    public const IMPORT_ORDER_KEY_ID = 'id';
    public const IMPORT_ORDER_KEY_DEPOT_ID = 'depotId';
    public const IMPORT_ORDER_KEY_STATUS = 'status';
    public const IMPORT_ORDER_KEY_CUSTOMER_UUID = 'customerUuid';

    public const IMPORT_ORDER_KEY_ADDRESS = 'address';
    public const IMPORT_ORDER_KEY_ADDRESS_STREET = 'street';
    public const IMPORT_ORDER_KEY_ADDRESS_HOUSE_NO = 'houseNumber';
    public const IMPORT_ORDER_KEY_ADDRESS_ZIP_CODE = 'zipCode';
    public const IMPORT_ORDER_KEY_ADDRESS_CITY = 'city';
    public const IMPORT_ORDER_KEY_ADDRESS_COUNTRY = 'country';

    public const IMPORT_ORDER_KEY_GEOLOCATION = 'geoLocation';
    public const IMPORT_ORDER_KEY_GEOLOCATION_LAT = 'lat';
    public const IMPORT_ORDER_KEY_GEOLOCATION_LNG = 'lng';

    public const IMPORT_ORDER_KEY_DATE_OF_DELIVERY = 'dateOfDelivery';

    public const IMPORT_ORDER_KEY_TIMESLOT = 'timeSlot';
    public const IMPORT_ORDER_KEY_TIMESLOT_START_TIME = 'startTime';
    public const IMPORT_ORDER_KEY_TIMESLOT_END_TIME = 'endTime';

    public const IMPORT_ORDER_KEY_STOP_TIME_MINUTES = 'stopTimeMinutes';

    public const IMPORT_ORDER_KEY_SHIPMENT = 'shipment';

    public const IMPORT_ORDER_KEY_SHIPMENT_LOAD = 'load';
    public const IMPORT_ORDER_KEY_SHIPMENT_LOAD_COUNT = 'itemCount';
    public const IMPORT_ORDER_KEY_SHIPMENT_LOAD_WEIGHT = 'weightKilogram';
    public const IMPORT_ORDER_KEY_SHIPMENT_LOAD_VOLUME = 'volumeCubicMeter';

    public const IMPORT_ORDER_KEY_SHIPMENT_RECIPIENT = 'recipient';
    public const IMPORT_ORDER_KEY_SHIPMENT_SENDER = 'sender';
    public const IMPORT_ORDER_KEY_SHIPMENT_LABEL = 'addressLabel';
    public const IMPORT_ORDER_KEY_SHIPMENT_BARCODE = 'barcode';
}
