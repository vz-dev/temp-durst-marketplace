<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 04.09.18
 * Time: 12:48
 */

namespace Pyz\Zed\Tour;


use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Shared\Integra\IntegraConstants;
use Pyz\Shared\Oms\OmsConstants;
use Pyz\Shared\Tour\TourConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class TourConfig extends AbstractBundleConfig
{
    public const NAME_TOUR_REFERENCE = 'TourReference';
    public const TOUR_REFERENCE_PREFIX_PART = 'TOUR';

    public const DEFAULT_TIME_FORMAT = 'H:i';
    public const DEFAULT_FORMAT_HOUR = 'H';
    public const DEFAULT_FORMAT_MINUTE = 'i';

    public const PREPARATION_MODIFY_FORMAT = '-%d minutes';

    public const DEFAULT_DATE_TIME_FORMAT = 'D d.m.y H:i';
    public const DEFAULT_PROJECT_TIME_ZONE = 'Europe/Berlin';

    public const DEFAULT_DATE_FORMAT = 'y-m-d';


    public const DEFAULT_DRIVER_APP_TOUR_FUTURE_CUTOFF = '+1day midnight';
    public const DEFAULT_DRIVER_APP_TOUR_PAST_CUTOFF = '-3 months midnight';

    public const CONCRETE_TOUR_STATUS_LABELS = [
        TourConstants::CONCRETE_TOUR_STATUS_ORDERABLE => 'Buchbar',
        TourConstants::CONCRETE_TOUR_STATUS_EMPTY => 'Leer',
        TourConstants::CONCRETE_TOUR_STATUS_PLANABLE => 'Planbar',
        TourConstants::CONCRETE_TOUR_STATUS_DELIVERABLE => 'Auslieferbar',
        TourConstants::CONCRETE_TOUR_STATUS_IN_DELIVERY => 'In Auslieferung',
        TourConstants::CONCRETE_TOUR_VIRTUAL_STATUS_PLANABLE_TO_IN_DELIVERY => 'Planbar / In Auslieferung',
        TourConstants::CONCRETE_TOUR_STATUS_DELIVERED => 'Ausgeliefert',
    ];

    public const DAYS_MAP = [
        'Mon' => 'monday',
        'Tue' => 'tuesday',
        'Wed' => 'wednesday',
        'Thu' => 'thursday',
        'Fri' => 'friday',
        'Sat' => 'saturday',
    ];

    public const DURST_ILN = '4399902370295';

    public const EDI_DEFAULT_DATE_FORMAT = 'd.m.Y';

    public const EDI_DEFAULT_TIME_FORMAT = 'H:i';

    public const EDI_DEFAULT_DATETIME_FORMAT = 'd.m.Y H:i';

    public const EDI_EDIFACT_NAME_REFERENCE = 'Edifact';
    public const EDI_EDIFACT_PREFIX_PART = 'EDIFACT';

    public const EDI_EDIFACT_DATETIME_FORMAT = 'YmdHi';
    public const EDI_EDIFACT_DATE_FORMAT = 'ymd';
    public const EDI_EDIFACT_TIME_FORMAT = 'Hi';

    public const TIME_INTERVAL_TEMPLATE = 'PT%dM';

    public const EDIFACT_TESTRUN = true;

    public const EDI_ATTRIBUTE_PRODUCT_NAME = 'name';
    public const EDI_ATTRIBUTE_PRODUCT_UNIT = 'unit';
    public const EDI_ATTRIBUTE_PRODUCT_GTIN = 'gtin';

    public const PHP_PATH_FOR_CONSOLE = '';

    protected const CONCRETE_TOUR_STATUS_MAP = [
        TourConstants::CONCRETE_TOUR_STATUS_ORDERABLE => [
            TourConstants::TOUR_STATE_NEW,
            TourConstants::TOUR_STATE_ORDERABLE,
            // TourConstants::TOUR_STATE_DELETED,
        ],
        TourConstants::CONCRETE_TOUR_STATUS_EMPTY => [
            TourConstants::TOUR_STATE_NO_VALID_ORDERS,
        ],
        TourConstants::CONCRETE_TOUR_STATUS_PLANABLE => [],
        TourConstants::CONCRETE_TOUR_STATUS_DELIVERABLE => [
            TourConstants::TOUR_STATE_GOODS_EXPORTABLE,
            TourConstants::TOUR_STATE_GOODS_EXPORT_FAILED,
            TourConstants::TOUR_STATE_MERCHANT_NOTIFIED_GOODS,
            TourConstants::TOUR_STATE_GOODS_EXPORTED,
            TourConstants::TOUR_STATE_IN_PLANNING,
            TourConstants::TOUR_STATE_LOADING,
            TourConstants::TOUR_STATE_JOURNEY_THERE,
        ],
        TourConstants::CONCRETE_TOUR_STATUS_IN_DELIVERY => [
            TourConstants::TOUR_STATE_IN_DELIVERY,
        ],
        TourConstants::CONCRETE_TOUR_STATUS_DELIVERED => [
            TourConstants::TOUR_STATE_EXPORTABLE_RETURNS,
            TourConstants::TOUR_STATE_RETURN_EXPORTABLE_AUTO,
            TourConstants::TOUR_STATE_RETURN_EXPORTABLE_MANUAL,
            TourConstants::TOUR_STATE_MERCHANT_NOTIFIED_RETURN,
            TourConstants::TOUR_STATE_RETURN_EXPORTED,
            TourConstants::TOUR_STATE_RETURN_EXPORT_FAILED,
            TourConstants::TOUR_STATE_RETURN_JOURNEY,
            TourConstants::TOUR_STATE_UNLOADING,
            TourConstants::TOUR_STATE_FINISHED,
        ],
    ];

    /**
     * @return string
     */
    public function getTimeFormat() : string
    {
        return $this
            ->get(DeliveryAreaConstants::TIME_SLOT_TIME_FORMAT, self::DEFAULT_TIME_FORMAT);
    }

    /**
     * @return string
     */
    public function getDateFormat() : string
    {
        return $this
            ->get(TourConstants::TOUR_DATE_FORMAT, self::DEFAULT_DATE_FORMAT);
    }

    /**
     * @return string
     */
    public function getDateTimeFormat() : string
    {
        return $this
            ->get(DeliveryAreaConstants::TIME_SLOT_DATE_TIME_FORMAT, self::DEFAULT_DATE_TIME_FORMAT);
    }

    /**
     * @return string
     */
    public function getProjectTimeZone() : string
    {
        return $this
            ->get(ApplicationConstants::PROJECT_TIMEZONE, self::DEFAULT_PROJECT_TIME_ZONE);
    }

    /**
     * @return array
     */
    public function getStateBlacklist() : array
    {
        return $this->get(TourConstants::TOUR_STATE_BLACK_LIST, ['new','order.state.confirmed','order.state.declined']);
    }

    /**
     * @return array
     */
    public function getActiveProcesses() : array
    {
        return $this->get(TourConstants::TOUR_ACTIVE_PROCESSES, ['RetailOrder','WholesaleOrder']);
    }

    /**
     * @return string
     */
    public function getConcreteTourFilteringEarliestAllowedDate(): string
    {
        return $this
            ->get(TourConstants::CONCRETE_TOUR_FILTERING_EARLIEST_ALLOWED_DATE);
    }

    /**
     * Separator for the sequence number
     *
     * @return string
     */
    public function getUniqueIdentifierSeparator() : string
    {
        return '-';
    }

    /**
     * @return SequenceNumberSettingsTransfer
     */
    public function getTourReferenceDefaults() : SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();

        $sequenceNumberSettingsTransfer->setName(self::NAME_TOUR_REFERENCE);

        $sequenceNumberPrefixParts = [];
        $sequenceNumberPrefixParts[] = Store::getInstance()->getStoreName();
        $sequenceNumberPrefixParts[] = self::TOUR_REFERENCE_PREFIX_PART;
        $prefix = implode($this->getUniqueIdentifierSeparator(), $sequenceNumberPrefixParts). $this->getUniqueIdentifierSeparator();
        $sequenceNumberSettingsTransfer->setPrefix($prefix);

        return $sequenceNumberSettingsTransfer;
    }

    /**
     * @return SequenceNumberSettingsTransfer
     */
    public function getEdifactReferenceDefaults(): SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();

        $name = self::EDI_EDIFACT_NAME_REFERENCE . $this->getUniqueIdentifierSeparator() . '%s' . $this->getUniqueIdentifierSeparator() . '%d';
        $sequenceNumberSettingsTransfer->setName($name);

        $sequenceNumberSettingsTransfer->setPrefix('');

        return $sequenceNumberSettingsTransfer;
    }

    /**
     * @return string
     */
    public function getDurstIlnNumber(): string
    {
        return $this
            ->get(TourConstants::DURST_ILN, self::DURST_ILN);
    }

    /**
     * @return array
     */
    public function getAcceptedOmsState(): array
    {
        return [
            $this->get(OmsConstants::OMS_RETAIL_ACCEPTED_STATE),
            $this->get(OmsConstants::OMS_WHOLESALE_ACCEPTED_STATE)
        ];
    }

    /**
     * @return array
     */
    public function getDeliveredOrPayedOmsState(): array
    {
        $states = $this->get(OmsConstants::OMS_WHOLESALE_PAYMENT_COMPLETE_STATES);
        $states[] = $this->get(OmsConstants::OMS_RETAIL_DELIVERED_STATE);

        return $states;
    }

    /**
     * @return bool
     */
    public function isEdifactTestrun(): bool
    {
        return $this
            ->get(TourConstants::EDIFACT_TESTRUN, self::EDIFACT_TESTRUN);
    }

    /**
     * @return string
     */
    public function getConcreteTourOrderableStatus(): string
    {
        return self::CONCRETE_TOUR_STATUS_LABELS[TourConstants::CONCRETE_TOUR_STATUS_ORDERABLE];
    }

    /**
     * @return string
     */
    public function getPhpPathForConsole(): string
    {
        return $this
            ->get(TourConstants::PHP_PATH_FOR_CONSOLE, self::PHP_PATH_FOR_CONSOLE);
    }

    /**
     * @return string
     */
    public function getStateMachineProcess(): string
    {
        return $this
            ->get(TourConstants::TOUR_STATE_MACHINE_PROCESS);
    }

    /**
     * @return string
     */
    public function getStateMachineInitialState(): string
    {
        return $this
            ->get(TourConstants::TOUR_INITIAL_STATE);
    }

    /**
     * @return array
     */
    public function getHiddenStateList(): array
    {
        return $this
            ->get(
                TourConstants::TOUR_HIDDEN_STATES,
                []
            );
    }

    /**
     * @return array
     */
    public function getConcreteTourStatusMap(): array
    {
        $concreteTourStatusMap = static::CONCRETE_TOUR_STATUS_MAP;

        $concreteTourStatusMap[TourConstants::CONCRETE_TOUR_VIRTUAL_STATUS_PLANABLE_TO_IN_DELIVERY] = array_merge(
            static::CONCRETE_TOUR_STATUS_MAP[TourConstants::CONCRETE_TOUR_STATUS_PLANABLE],
            static::CONCRETE_TOUR_STATUS_MAP[TourConstants::CONCRETE_TOUR_STATUS_DELIVERABLE],
            static::CONCRETE_TOUR_STATUS_MAP[TourConstants::CONCRETE_TOUR_STATUS_IN_DELIVERY]
        );

        return $concreteTourStatusMap;
    }

    /**
     * @return string
     */
    public function getDriverAppTourPastCutOff() : string
    {
        return $this
            ->get(TourConstants::DRIVER_APP_TOUR_PAST_CUTOFF, self::DEFAULT_DRIVER_APP_TOUR_PAST_CUTOFF);
    }

    /**
     * @return string
     */
    public function getDriverAppTourFutureCutOff() : string
    {
        return $this
            ->get(TourConstants::DRIVER_APP_TOUR_FUTURE_CUTOFF, self::DEFAULT_DRIVER_APP_TOUR_FUTURE_CUTOFF);
    }

    /**
     * @return string
     */
    public function getDriverAppTourFutureCutOffIntegra() : string
    {
        return IntegraConstants::DRIVER_APP_TOUR_FUTURE_CUTOFF_INTEGRA;
    }

    /**
     * @return array
     */
    public function getIntegraGbzPaymentMethods() : array
    {
        return IntegraConstants::INTEGRA_GBZ_PAYMENT_METHOD_MAP;
    }

    /**
     * @return array
     */
    public function getIntegraGbzPaymentCodes() : array
    {
        return IntegraConstants::INTEGRA_GBZ_PAYMENT_CODE_MAP;
    }

    /**
     * @return array
     */
    public function getEdiClientCurlOptions(): array
    {
        return $this
            ->get(
                TourConstants::EDI_CLIENT_CURL_OPTIONS,
                []
            );
    }
}
