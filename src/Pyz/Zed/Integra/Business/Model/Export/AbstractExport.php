<?php
/**
 * Durst - project - AbstractExport.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.11.20
 * Time: 14:18
 */

namespace Pyz\Zed\Integra\Business\Model\Export;

use DateTime;
use DateTimeZone;
use PDOStatement;
use Pyz\Zed\Integra\Business\Exception\DatabaseException;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;

abstract class AbstractExport implements ExportInterface
{
    protected const HEADER_ORDER_NO = 'Bestellnr.';
    protected const HEADER_RECEIPT_DID = 'Beleg-DID';
    protected const HEADER_SHIPMENT_TYPE = 'Versandart';
    protected const HEADER_TOUR_TRIP_NO = 'NrTourFahrt';
    protected const HEADER_TOUR_NR = 'Tour-Nr';
    protected const HEADER_ORDER_DATE = 'Bestelldatum';
    protected const HEADER_DELIVERY_START = 'Lieferzeit Start';
    protected const HEADER_DELIVERY_END = 'Lieferzeit End';
    protected const HEADER_COMMENT = 'Freitext';
    protected const HEADER_SELLER_GLN = 'Seller GLN';
    protected const HEADER_CUSTOMER_NO = 'Kundennr.';
    protected const HEADER_POSITION_NO = 'Positions-Nr.';
    protected const HEADER_POSITION_DID = 'Positions-DID';
    protected const HEADER_SKU = 'SKU';
    protected const HEADER_PRODUCT = 'Bezeichnung';
    protected const HEADER_QUANTITY = 'Anzahl';
    protected const HEADER_TYPE = 'Kasten';
    protected const HEADER_STATE = 'Status';
    protected const HEADER_TRANSACTIONCODE_UNZER = 'Transaktionscode Unzer';
    protected const HEADER_RETURN_REASON = 'Retourgrund';
    protected const HEADER_AMOUNT_PAID = 'BargeldBetrag';

    protected const HEADER = [
        self::HEADER_ORDER_NO,
        self::HEADER_RECEIPT_DID,
        self::HEADER_SHIPMENT_TYPE,
        self::HEADER_TOUR_NR,
        self::HEADER_ORDER_DATE,
        self::HEADER_DELIVERY_START,
        self::HEADER_DELIVERY_END,
        self::HEADER_COMMENT,
        self::HEADER_SELLER_GLN,
        self::HEADER_CUSTOMER_NO,
        self::HEADER_POSITION_DID,
        self::HEADER_POSITION_NO,
        self::HEADER_SKU,
        self::HEADER_PRODUCT,
        self::HEADER_QUANTITY,
        self::HEADER_TYPE,
        self::HEADER_STATE,
        self::HEADER_TRANSACTIONCODE_UNZER,
        self::HEADER_RETURN_REASON,
        self::HEADER_TOUR_TRIP_NO,
        self::HEADER_AMOUNT_PAID,
    ];

    protected const SELLER_GLN = '4329756000000';
    protected const SHIPMENT_TYPE = '01';

    protected const UNIT_TYPE_DEFAULT = self::UNIT_TYPE_CASE;
    protected const UNIT_TYPE_CASE = 'KA';
    protected const UNIT_TYPE_BOTTLE = 'FL';
    protected const UNIT_TYPE_CAN = 'DO';
    protected const UNIT_TYPE_DISPLAY = 'DP';
    protected const UNIT_TYPE_BARREL = 'FA';
    protected const UNIT_TYPE_CARTON = 'KT';
    protected const UNIT_TYPE_PACKAGE = 'PG';
    protected const UNIT_TYPE_PIECE = 'ST';
    protected const UNIT_TYPE_TRAY = 'TR';

    protected const ALLOWED_UNIT_TYPES = [
        self::UNIT_TYPE_BOTTLE,
        self::UNIT_TYPE_CASE,
        self::UNIT_TYPE_CAN,
        self::UNIT_TYPE_DISPLAY,
        self::UNIT_TYPE_BARREL,
        self::UNIT_TYPE_CARTON,
        self::UNIT_TYPE_PACKAGE,
        self::UNIT_TYPE_PIECE,
        self::UNIT_TYPE_TRAY,
    ];

    protected const RETURNED_DEPOSIT_BOTTLES = 'BOTTLES';
    protected const RETURNED_DEPOSITS_CASES = 'CASES';
    protected const RETURNED_DEPOSITS_DEPOSIT = 'DEPOSIT';

    protected const POSSIBLE_RETURNED_DEPOSIT_TYPES = [
        self::RETURNED_DEPOSIT_BOTTLES,
        self::RETURNED_DEPOSITS_CASES,
        self::RETURNED_DEPOSITS_DEPOSIT,
    ];

    // @todo was kommt hier jeweils hin?
    protected const RETURNDEPOSIT_TYPES_TO_INTEGRA_TYPES = [
        self::RETURNED_DEPOSIT_BOTTLES => self::UNIT_TYPE_BOTTLE,
        self::RETURNED_DEPOSITS_CASES => self::UNIT_TYPE_CASE,
        self::RETURNED_DEPOSITS_DEPOSIT => self::UNIT_TYPE_CASE,
    ];

    protected const DATE_TIME_STRING_FORMAT = 'Y-m-d H:i:s.u';
    protected const DATE_FORMAT = 'Y-m-d';
    protected const NA_STRING = 'n/a';

    protected const INTEGRA_EXPORT_INVALID_CHARS = ['|', "\r\n", "\r", "\n"];

    protected const INTEGRA_MERCHANT_SKU_PREFIX = 'integra_%s_';

    /**
     * @todo Ugly as fuck aber GBZ Besteht drauf... in Zukunft müssen die korrekten
     * Versandarten mit den konkreten Zeitfenster gemappt werden damit wir sinnvoll die
     * Versandart ermiteln können zum Start hartkodiert für die beiden PLZs
     */
    protected const NR_TOUR_NO = '08';
    protected const ZIP_CODES_GBZ_START = ['85221', '85757'];
    protected const CONCRETE_TIMESLOT_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var int[]
     */
    protected $affectedOrderIds = [];

    /**
     * @var IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var array
     */
    protected $itemsPos = [];

    /**
     * @var int
     */
    protected $idBranch;

    /**
     * @param PDOStatement $statement
     *
     * @return void
     */
    protected function executeUpdateQuery(PDOStatement $statement): void
    {
        $success = $statement->execute();
        if ($success !== true) {
            throw DatabaseException::update();
        }
    }

    /**
     * @param array $row
     *
     * @return array
     */
    protected function transformRow(array $row): array
    {
        $result = [];
        foreach (static::HEADER as $column) {
            $result[] = $row[$column];
        }

        return $result;
    }

    /**
     * @param string|null $dateTimeString
     * @return string
     */
    protected function getFormattedDate(?string $dateTimeString = null): string
    {
        if ($dateTimeString === null) {
            return static::NA_STRING;
        }

        $dateTime = DateTime::createFromFormat(self::DATE_TIME_STRING_FORMAT, $dateTimeString);

        return $dateTime->format(static::DATE_FORMAT);
    }


    protected function getBerlinEuropeTimeFromUtc(?string $dateTimeString = null): string
    {
        if ($dateTimeString === null) {
            return static::NA_STRING;
        }

        $dateTime = DateTime::createFromFormat(self::CONCRETE_TIMESLOT_DATETIME_FORMAT, $dateTimeString, new DateTimeZone('UTC'));
        $dateTime->setTimeZone(new DateTimeZone('Europe/Berlin'));
        return $dateTime->format(self::CONCRETE_TIMESLOT_DATETIME_FORMAT);
    }

    /**
     * @param string $merchantSku
     *
     * @return string
     */
    protected function getUnitTypeFromMerchantSku(string $merchantSku) : string
    {
        $skuParts = explode('_', $merchantSku);

        if (count($skuParts) > 1) {
            if (in_array(end($skuParts), static::ALLOWED_UNIT_TYPES) === true) {
                return end($skuParts);
            }
        }

        return static::UNIT_TYPE_DEFAULT;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function getStringWithNumbersOnly(string $string) : string
    {
        $str = str_replace(sprintf(self::INTEGRA_MERCHANT_SKU_PREFIX, $this->idBranch),'', $string);
        return preg_replace("/[^0-9]/", "", $str);
    }

    /**
     * @param int $orderId
     * @param string $merchantSku
     * @param string $state
     *
     * @return int
     */
    protected function getPosByOrderIdAndSkuState(int $orderId, string $merchantSku, string $state) : int
    {
        if (array_key_exists($orderId, $this->itemsPos) !== true) {
            $this->itemsPos[$orderId] = [];
        }

        if (in_array($merchantSku.$state, $this->itemsPos[$orderId]) === false) {
            $this->itemsPos[$orderId][] = $merchantSku.$state;
        }

        return count($this->itemsPos[$orderId]);
    }

    /**
     * @param string $date
     * @param string $zipCode
     * @param string|null $tourNo
     *
     * @return string
     */
    protected function getVersandartByDateAndZip(string $date, string $zipCode, ?string $tourNo) : string
    {
        $dayOfWeek = date("N", strtotime($date));

        if($tourNo !== null)
        {
            return sprintf('%s%s', $dayOfWeek, $tourNo);
        }

        if(in_array($zipCode, static::ZIP_CODES_GBZ_START)){
            return sprintf('%s%s', $dayOfWeek, static::NR_TOUR_NO);
        }

        return '';
    }

    /**
     * @param string|null $string
     * @return string
     */
    protected function stripIllegalChars(?string $string) :string
    {
        if($string === null)
        {
            return '';
        }

        return str_replace(static::INTEGRA_EXPORT_INVALID_CHARS,'',$string);
    }

    /**
     * @param string $date
     * @param string|null $deliveryWindowNo
     * @return int
     */
    protected function getTourTripNoFromDateTime(string $date, ?string $deliveryWindowNo) : int
    {
        if($deliveryWindowNo !== null)
        {
            return intval($deliveryWindowNo);
        }

        if(str_replace(':', '', substr($this->getBerlinEuropeTimeFromUtc($date), -8)) < '150100'){
            return 1;
        }

        return 2;
    }
}
