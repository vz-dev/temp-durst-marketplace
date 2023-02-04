<?php
/**
 * Durst - project - RealaxExportMapper.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.03.20
 * Time: 11:35
 */

namespace Pyz\Zed\Accounting\Business\Mapper;


class RealaxExportMapper
{
    public const HEADER_KEY = 'header';
    public const BOOKING_HEAD_KEY = 'booking_head';
    public const BOOKING_POS_KEY = 'booking_positions';

    public const EXPORT_TYPE = 'export_type';

    public const HEADER_IDENTIFIER = 'identifier';
    public const HEADER_NUMBER_HANDOVER = 'number_handover';
    public const HEADER_CREATED_AT = 'created_at';
    public const HEADER_APPLICATION_NUMBER = 'application_number';
    public const HEADER_RELEASE = 'release';
    public const HEADER_ACCOUNTING_AREA = 'accounting_area';
    public const HEADER_UPDATED_AT = 'updated_at';
    public const HEADER_DESCRIPTION = 'description';
    public const HEADER_CONVERT_ANSI = 'convert_ansi';
    public const HEADER_CARRYOVER = 'carryover';

    public const BOOKING_HEAD_IDENTIFIER = 'identifier';
    public const BOOKING_HEAD_NUMBER_HANDOVER = 'number_handover';
    public const BOOKING_HEAD_POSITION_HANDOVER = 'position_handover';
    public const BOOKING_HEAD_ACCOUNT_TYPE = 'account_type';
    public const BOOKING_HEAD_ACCOUNT = 'account';
    public const BOOKING_HEAD_DEBITS = 'debits';
    public const BOOKING_HEAD_DOCUMENT_NUMBER = 'document_number';
    public const BOOKING_HEAD_FOREIGN_DOCUMENT_NUMBER = 'foreign_document_number';
    public const BOOKING_HEAD_OI_NUMBER = 'oi_number';
    public const BOOKING_HEAD_DOCUMENT_DATE = 'document_date';
    public const BOOKING_HEAD_BOOKING_TYPE_NUMBER = 'booking_type_number';
    public const BOOKING_HEAD_BOOKING_STATE = 'booking_state';
    public const BOOKING_HEAD_BOOKING_TEXT = 'booking_text';
    public const BOOKING_HEAD_CURRENCY = 'currency';
    public const BOOKING_HEAD_AMOUNT_CURRENCY = 'amount_currency';
    public const BOOKING_HEAD_AMOUNT = 'amount';
    public const BOOKING_HEAD_AMOUNT_DISCOUNT_CURRENCY = 'amount_discount_currency';
    public const BOOKING_HEAD_AMOUNT_DISCOUNT = 'amount_discount';
    public const BOOKING_HEAD_PAYMENT_TYPE = 'payment_type';
    public const BOOKING_HEAD_PAYMENT_CONDITION = 'payment_condition';
    public const BOOKING_HEAD_FIRST_DISCOUNT_DATE = 'first_discount_date';
    public const BOOKING_HEAD_SECOND_DISCOUNT_DATE = 'second_discount_date';
    public const BOOKING_HEAD_FIRST_DISCOUNT_PERCENT = 'first_discount_percent';
    public const BOOKING_HEAD_SECOND_DISCOUNT_PERCENT = 'second_discount_percent';
    public const BOOKING_HEAD_DUE_DATE_NET = 'due_date_net';
    public const BOOKING_HEAD_VALUE_DATE = 'value_date';
    public const BOOKING_HEAD_VALUE_DAYS = 'value_days';
    public const BOOKING_HEAD_AUTO_DUNNING = 'auto_dunning';
    public const BOOKING_HEAD_AUTO_REGULATION = 'auto_regulation';
    public const BOOKING_HEAD_DISCOUNTABLE_AMOUNT_CURRENCY = 'discountable_amount_currency';
    public const BOOKING_HEAD_DISCOUNTABLE_AMOUNT = 'discountable_amount';
    public const BOOKING_HEAD_PERIODIC_BOOKING_START = 'periodic_booking_start';
    public const BOOKING_HEAD_PERIODIC_BOOKING_END = 'periodic_booking_end';
    public const BOOKING_HEAD_PERIOD_CYCLE = 'period_cycle';
    public const BOOKING_HEAD_COST_UNIT = 'cost_unit';
    public const BOOKING_HEAD_COST_OBJECT = 'cost_object';
    public const BOOKING_HEAD_COST_TYPE = 'cost_type';
    public const BOOKING_HEAD_PROJECT = 'project';
    public const BOOKING_HEAD_BASE_CURRENCY = 'base_currency';
    public const BOOKING_HEAD_EXCHANGE_RATE = 'exchange_rate';
    public const BOOKING_HEAD_BEARING = 'bearing';
    public const BOOKING_HEAD_EXCHANGE_RATE_TO_EURO = 'exchange_rate_to_euro';
    public const BOOKING_HEAD_EXCHANGE_RATE_TO_BASE = 'exchange_rate_to_base';
    public const BOOKING_HEAD_DUNNING_GROUP = 'dunning_group';
    public const BOOKING_HEAD_DUNNING_LEVEL = 'dunning_level';
    public const BOOKING_HEAD_DUNNING_DATE = 'dunning_date';
    public const BOOKING_HEAD_COMPANY_CODE = 'company_code';
    public const BOOKING_HEAD_PAGINATION_NUMBER = 'pagination_number';
    public const BOOKING_HEAD_REFERENCE_NUMBER = 'reference_number';

    public const BOOKING_POS_IDENTIFIER = 'identifier';
    public const BOOKING_POS_NUMBER_HANDOVER = 'number_handover';
    public const BOOKING_POS_POSITION_HANDOVER = 'position_handover';
    public const BOOKING_POS_OFFSET_ACCOUNT = 'offset_account';
    public const BOOKING_POS_ACCOUNT_TYPE = 'account_type';
    public const BOOKING_POS_ACCOUNT = 'account';
    public const BOOKING_POS_DEBITS = 'debits';
    public const BOOKING_POS_BOOKING_TEXT = 'booking_text';
    public const BOOKING_POS_COST_UNIT = 'cost_unit';
    public const BOOKING_POS_COST_OBJECT = 'cost_object';
    public const BOOKING_POS_COST_TYPE = 'cost_type';
    public const BOOKING_POS_PROJECT = 'project';
    public const BOOKING_POS_TAX_TYPE = 'tax_type';
    public const BOOKING_POS_CUSTOMS_DUTY_KEY = 'customs_duty_key';
    public const BOOKING_POS_AMOUNT_CURRENCY_NET = 'amount_currency_net';
    public const BOOKING_POS_AMOUNT_NET = 'amount_net';
    public const BOOKING_POS_TAX_AMOUNT_CURRENCY = 'tax_amount_currency';
    public const BOOKING_POS_TAX_AMOUNT = 'tax_amount';
    public const BOOKING_POS_QUANTITY_UNIT = 'quantity_unit';
    public const BOOKING_POS_QUANTITY = 'quantity';
    public const BOOKING_POS_AMOUNT_DISCOUNT_CURRENCY = 'amount_discount_currency';
    public const BOOKING_POS_AMOUNT_DISCOUNT = 'amount_discount';


    public const HEADER_IDENTIFIER_POSITION = 0;
    public const HEADER_NUMBER_HANDOVER_POSITION = 1;
    public const HEADER_CREATED_AT_POSITION = 2;
    public const HEADER_APPLICATION_NUMBER_POSITION = 3;
    public const HEADER_RELEASE_POSITION = 4;
    public const HEADER_ACCOUNTING_AREA_POSITION = 5;
    public const HEADER_UPDATED_AT_POSITION = 6;
    public const HEADER_DESCRIPTION_POSITION = 7;
    public const HEADER_CONVERT_ANSI_POSITION = 8;
    public const HEADER_CARRYOVER_POSITION = 9;

    public const BOOKING_HEAD_IDENTIFIER_POSITION = 0;
    public const BOOKING_HEAD_NUMBER_HANDOVER_POSITION = 1;
    public const BOOKING_HEAD_POSITION_HANDOVER_POSITION = 2;
    public const BOOKING_HEAD_ACCOUNT_TYPE_POSITION = 3;
    public const BOOKING_HEAD_ACCOUNT_POSITION = 4;
    public const BOOKING_HEAD_DEBITS_POSITION = 5;
    public const BOOKING_HEAD_DOCUMENT_NUMBER_POSITION = 6;
    public const BOOKING_HEAD_FOREIGN_DOCUMENT_NUMBER_POSITION = 7;
    public const BOOKING_HEAD_OI_NUMBER_POSITION = 8;
    public const BOOKING_HEAD_DOCUMENT_DATE_POSITION = 9;
    public const BOOKING_HEAD_BOOKING_TYPE_NUMBER_POSITION = 10;
    public const BOOKING_HEAD_BOOKING_STATE_POSITION = 11;
    public const BOOKING_HEAD_BOOKING_TEXT_POSITION = 12;
    public const BOOKING_HEAD_CURRENCY_POSITION = 13;
    public const BOOKING_HEAD_AMOUNT_CURRENCY_POSITION = 14;
    public const BOOKING_HEAD_AMOUNT_POSITION = 15;
    public const BOOKING_HEAD_AMOUNT_DISCOUNT_CURRENCY_POSITION = 16;
    public const BOOKING_HEAD_AMOUNT_DISCOUNT_POSITION = 17;
    public const BOOKING_HEAD_PAYMENT_TYPE_POSITION = 18;
    public const BOOKING_HEAD_PAYMENT_CONDITION_POSITION = 19;
    public const BOOKING_HEAD_FIRST_DISCOUNT_DATE_POSITION = 20;
    public const BOOKING_HEAD_SECOND_DISCOUNT_DATE_POSITION = 21;
    public const BOOKING_HEAD_FIRST_DISCOUNT_PERCENT_POSITION = 22;
    public const BOOKING_HEAD_SECOND_DISCOUNT_PERCENT_POSITION = 23;
    public const BOOKING_HEAD_DUE_DATE_NET_POSITION = 24;
    public const BOOKING_HEAD_VALUE_DATE_POSITION = 25;
    public const BOOKING_HEAD_VALUE_DAYS_POSITION = 26;
    public const BOOKING_HEAD_AUTO_DUNNING_POSITION = 27;
    public const BOOKING_HEAD_AUTO_REGULATION_POSITION = 28;
    public const BOOKING_HEAD_DISCOUNTABLE_AMOUNT_CURRENCY_POSITION = 29;
    public const BOOKING_HEAD_DISCOUNTABLE_AMOUNT_POSITION = 30;
    public const BOOKING_HEAD_PERIODIC_BOOKING_START_POSITION = 31;
    public const BOOKING_HEAD_PERIODIC_BOOKING_END_POSITION = 32;
    public const BOOKING_HEAD_PERIOD_CYCLE_POSITION = 33;
    public const BOOKING_HEAD_COST_UNIT_POSITION = 34;
    public const BOOKING_HEAD_COST_OBJECT_POSITION = 35;
    public const BOOKING_HEAD_COST_TYPE_POSITION = 36;
    public const BOOKING_HEAD_PROJECT_POSITION = 37;
    public const BOOKING_HEAD_BASE_CURRENCY_POSITION = 38;
    public const BOOKING_HEAD_EXCHANGE_RATE_POSITION = 39;
    public const BOOKING_HEAD_BEARING_POSITION = 40;
    public const BOOKING_HEAD_EXCHANGE_RATE_TO_EURO_POSITION = 41;
    public const BOOKING_HEAD_EXCHANGE_RATE_TO_BASE_POSITION = 42;
    public const BOOKING_HEAD_DUNNING_GROUP_POSITION = 43;
    public const BOOKING_HEAD_DUNNING_LEVEL_POSITION = 44;
    public const BOOKING_HEAD_DUNNING_DATE_POSITION = 45;
    public const BOOKING_HEAD_COMPANY_CODE_POSITION = 46;
    public const BOOKING_HEAD_PAGINATION_NUMBER_POSITION = 47;
    public const BOOKING_HEAD_REFERENCE_NUMBER_POSITION = 48;

    public const BOOKING_POS_IDENTIFIER_POSITION = 0;
    public const BOOKING_POS_NUMBER_HANDOVER_POSITION = 1;
    public const BOOKING_POS_POSITION_HANDOVER_POSITION = 2;
    public const BOOKING_POS_OFFSET_ACCOUNT_POSITION = 3;
    public const BOOKING_POS_ACCOUNT_TYPE_POSITION = 4;
    public const BOOKING_POS_ACCOUNT_POSITION = 5;
    public const BOOKING_POS_DEBITS_POSITION = 6;
    public const BOOKING_POS_BOOKING_TEXT_POSITION = 7;
    public const BOOKING_POS_COST_UNIT_POSITION = 8;
    public const BOOKING_POS_COST_OBJECT_POSITION = 9;
    public const BOOKING_POS_COST_TYPE_POSITION = 10;
    public const BOOKING_POS_PROJECT_POSITION = 11;
    public const BOOKING_POS_TAX_TYPE_POSITION = 12;
    public const BOOKING_POS_CUSTOMS_DUTY_KEY_POSITION = 13;
    public const BOOKING_POS_AMOUNT_CURRENCY_NET_POSITION = 14;
    public const BOOKING_POS_AMOUNT_NET_POSITION = 15;
    public const BOOKING_POS_TAX_AMOUNT_CURRENCY_POSITION = 16;
    public const BOOKING_POS_TAX_AMOUNT_POSITION = 17;
    public const BOOKING_POS_QUANTITY_UNIT_POSITION = 18;
    public const BOOKING_POS_QUANTITY_POSITION = 19;
    public const BOOKING_POS_AMOUNT_DISCOUNT_CURRENCY_POSITION = 20;
    public const BOOKING_POS_AMOUNT_DISCOUNT_POSITION = 21;

    /**
     * @param array $payload
     * @return array
     */
    public function map(array $payload): array
    {
        switch ($payload[static::EXPORT_TYPE]) {
            case static::HEADER_KEY:
                return $this
                    ->mapHeader(
                        $payload
                    );
            case static::BOOKING_HEAD_KEY:
                return $this
                    ->mapBookingHead(
                        $payload
                    );
            case static::BOOKING_POS_KEY:
                return $this
                    ->mapBookingPositions(
                        $payload
                    );
        }

        return $payload;
    }

    /**
     * @param array $headPayload
     * @return array
     */
    protected function mapHeader(array $headPayload): array
    {
        return [
            static::HEADER_IDENTIFIER_POSITION => $headPayload[static::HEADER_IDENTIFIER],
            static::HEADER_NUMBER_HANDOVER_POSITION => $headPayload[static::HEADER_NUMBER_HANDOVER],
            static::HEADER_CREATED_AT_POSITION => $headPayload[static::HEADER_CREATED_AT],
            static::HEADER_APPLICATION_NUMBER_POSITION => $headPayload[static::HEADER_APPLICATION_NUMBER],
            static::HEADER_RELEASE_POSITION => $headPayload[static::HEADER_RELEASE],
            static::HEADER_ACCOUNTING_AREA_POSITION => $headPayload[static::HEADER_ACCOUNTING_AREA],
            static::HEADER_UPDATED_AT_POSITION => $headPayload[static::HEADER_UPDATED_AT],
            static::HEADER_DESCRIPTION_POSITION => $headPayload[static::HEADER_DESCRIPTION],
            static::HEADER_CONVERT_ANSI_POSITION => $headPayload[static::HEADER_CONVERT_ANSI],
            static::HEADER_CARRYOVER_POSITION => $headPayload[static::HEADER_CARRYOVER]
        ];
    }

    /**
     * @param array $bookingPayload
     * @return array
     */
    protected function mapBookingHead(array $bookingPayload): array
    {
        return [
            static::BOOKING_HEAD_IDENTIFIER_POSITION => $bookingPayload[static::BOOKING_HEAD_IDENTIFIER],
            static::BOOKING_HEAD_NUMBER_HANDOVER_POSITION => $bookingPayload[static::BOOKING_HEAD_NUMBER_HANDOVER],
            static::BOOKING_HEAD_POSITION_HANDOVER_POSITION => $bookingPayload[static::BOOKING_HEAD_POSITION_HANDOVER],
            static::BOOKING_HEAD_ACCOUNT_TYPE_POSITION => $bookingPayload[static::BOOKING_HEAD_ACCOUNT_TYPE],
            static::BOOKING_HEAD_ACCOUNT_POSITION => $bookingPayload[static::BOOKING_HEAD_ACCOUNT],
            static::BOOKING_HEAD_DEBITS_POSITION => $bookingPayload[static::BOOKING_HEAD_DEBITS],
            static::BOOKING_HEAD_DOCUMENT_NUMBER_POSITION => $bookingPayload[static::BOOKING_HEAD_DOCUMENT_NUMBER],
            static::BOOKING_HEAD_FOREIGN_DOCUMENT_NUMBER_POSITION => $bookingPayload[static::BOOKING_HEAD_FOREIGN_DOCUMENT_NUMBER],
            static::BOOKING_HEAD_OI_NUMBER_POSITION => $bookingPayload[static::BOOKING_HEAD_OI_NUMBER],
            static::BOOKING_HEAD_DOCUMENT_DATE_POSITION => $bookingPayload[static::BOOKING_HEAD_DOCUMENT_DATE],
            static::BOOKING_HEAD_BOOKING_TYPE_NUMBER_POSITION => $bookingPayload[static::BOOKING_HEAD_BOOKING_TYPE_NUMBER],
            static::BOOKING_HEAD_BOOKING_STATE_POSITION => $bookingPayload[static::BOOKING_HEAD_BOOKING_STATE],
            static::BOOKING_HEAD_BOOKING_TEXT_POSITION => $bookingPayload[static::BOOKING_HEAD_BOOKING_TEXT],
            static::BOOKING_HEAD_CURRENCY_POSITION => $bookingPayload[static::BOOKING_HEAD_CURRENCY],
            static::BOOKING_HEAD_AMOUNT_CURRENCY_POSITION => $bookingPayload[static::BOOKING_HEAD_AMOUNT_CURRENCY],
            static::BOOKING_HEAD_AMOUNT_POSITION => $bookingPayload[static::BOOKING_HEAD_AMOUNT],
            static::BOOKING_HEAD_AMOUNT_DISCOUNT_CURRENCY_POSITION => $bookingPayload[static::BOOKING_HEAD_AMOUNT_DISCOUNT_CURRENCY],
            static::BOOKING_HEAD_AMOUNT_DISCOUNT_POSITION => $bookingPayload[static::BOOKING_HEAD_AMOUNT_DISCOUNT],
            static::BOOKING_HEAD_PAYMENT_TYPE_POSITION => $bookingPayload[static::BOOKING_HEAD_PAYMENT_TYPE],
            static::BOOKING_HEAD_PAYMENT_CONDITION_POSITION => $bookingPayload[static::BOOKING_HEAD_PAYMENT_CONDITION],
            static::BOOKING_HEAD_FIRST_DISCOUNT_DATE_POSITION => $bookingPayload[static::BOOKING_HEAD_FIRST_DISCOUNT_DATE],
            static::BOOKING_HEAD_SECOND_DISCOUNT_DATE_POSITION => $bookingPayload[static::BOOKING_HEAD_SECOND_DISCOUNT_DATE],
            static::BOOKING_HEAD_FIRST_DISCOUNT_PERCENT_POSITION => $bookingPayload[static::BOOKING_HEAD_FIRST_DISCOUNT_PERCENT],
            static::BOOKING_HEAD_SECOND_DISCOUNT_PERCENT_POSITION => $bookingPayload[static::BOOKING_HEAD_SECOND_DISCOUNT_PERCENT],
            static::BOOKING_HEAD_DUE_DATE_NET_POSITION => $bookingPayload[static::BOOKING_HEAD_DUE_DATE_NET],
            static::BOOKING_HEAD_VALUE_DATE_POSITION => $bookingPayload[static::BOOKING_HEAD_VALUE_DATE],
            static::BOOKING_HEAD_VALUE_DAYS_POSITION => $bookingPayload[static::BOOKING_HEAD_VALUE_DAYS],
            static::BOOKING_HEAD_AUTO_DUNNING_POSITION => $bookingPayload[static::BOOKING_HEAD_AUTO_DUNNING],
            static::BOOKING_HEAD_AUTO_REGULATION_POSITION => $bookingPayload[static::BOOKING_HEAD_AUTO_REGULATION],
            static::BOOKING_HEAD_DISCOUNTABLE_AMOUNT_CURRENCY_POSITION => $bookingPayload[static::BOOKING_HEAD_DISCOUNTABLE_AMOUNT_CURRENCY],
            static::BOOKING_HEAD_DISCOUNTABLE_AMOUNT_POSITION => $bookingPayload[static::BOOKING_HEAD_DISCOUNTABLE_AMOUNT],
            static::BOOKING_HEAD_PERIODIC_BOOKING_START_POSITION => $bookingPayload[static::BOOKING_HEAD_PERIODIC_BOOKING_START],
            static::BOOKING_HEAD_PERIODIC_BOOKING_END_POSITION => $bookingPayload[static::BOOKING_HEAD_PERIODIC_BOOKING_END],
            static::BOOKING_HEAD_PERIOD_CYCLE_POSITION => $bookingPayload[static::BOOKING_HEAD_PERIOD_CYCLE],
            static::BOOKING_HEAD_COST_UNIT_POSITION => $bookingPayload[static::BOOKING_HEAD_COST_UNIT],
            static::BOOKING_HEAD_COST_OBJECT_POSITION => $bookingPayload[static::BOOKING_HEAD_COST_OBJECT],
            static::BOOKING_HEAD_COST_TYPE_POSITION => $bookingPayload[static::BOOKING_HEAD_COST_TYPE],
            static::BOOKING_HEAD_PROJECT_POSITION => $bookingPayload[static::BOOKING_HEAD_PROJECT],
            static::BOOKING_HEAD_BASE_CURRENCY_POSITION => $bookingPayload[static::BOOKING_HEAD_BASE_CURRENCY],
            static::BOOKING_HEAD_EXCHANGE_RATE_POSITION => $bookingPayload[static::BOOKING_HEAD_EXCHANGE_RATE],
            static::BOOKING_HEAD_BEARING_POSITION => $bookingPayload[static::BOOKING_HEAD_BEARING],
            static::BOOKING_HEAD_EXCHANGE_RATE_TO_EURO_POSITION => $bookingPayload[static::BOOKING_HEAD_EXCHANGE_RATE_TO_EURO],
            static::BOOKING_HEAD_EXCHANGE_RATE_TO_BASE_POSITION => $bookingPayload[static::BOOKING_HEAD_EXCHANGE_RATE_TO_BASE],
            static::BOOKING_HEAD_DUNNING_GROUP_POSITION => $bookingPayload[static::BOOKING_HEAD_DUNNING_GROUP],
            static::BOOKING_HEAD_DUNNING_LEVEL_POSITION => $bookingPayload[static::BOOKING_HEAD_DUNNING_LEVEL],
            static::BOOKING_HEAD_DUNNING_DATE_POSITION => $bookingPayload[static::BOOKING_HEAD_DUNNING_DATE],
            static::BOOKING_HEAD_COMPANY_CODE_POSITION => $bookingPayload[static::BOOKING_HEAD_COMPANY_CODE],
            static::BOOKING_HEAD_PAGINATION_NUMBER_POSITION => $bookingPayload[static::BOOKING_HEAD_PAGINATION_NUMBER],
            static::BOOKING_HEAD_REFERENCE_NUMBER_POSITION => $bookingPayload[static::BOOKING_HEAD_REFERENCE_NUMBER]
        ];
    }

    /**
     * @param array $position
     * @return array
     */
    protected function mapBookingPositions(array $position): array
    {
        return [
            static::BOOKING_POS_IDENTIFIER_POSITION => $position[static::BOOKING_POS_IDENTIFIER],
            static::BOOKING_POS_NUMBER_HANDOVER_POSITION => $position[static::BOOKING_POS_NUMBER_HANDOVER],
            static::BOOKING_POS_POSITION_HANDOVER_POSITION => $position[static::BOOKING_POS_POSITION_HANDOVER],
            static::BOOKING_POS_OFFSET_ACCOUNT_POSITION => $position[static::BOOKING_POS_OFFSET_ACCOUNT],
            static::BOOKING_POS_ACCOUNT_TYPE_POSITION => $position[static::BOOKING_POS_ACCOUNT_TYPE],
            static::BOOKING_POS_ACCOUNT_POSITION => $position[static::BOOKING_POS_ACCOUNT],
            static::BOOKING_POS_DEBITS_POSITION => $position[static::BOOKING_POS_DEBITS],
            static::BOOKING_POS_BOOKING_TEXT_POSITION => $position[static::BOOKING_POS_BOOKING_TEXT],
            static::BOOKING_POS_COST_UNIT_POSITION => $position[static::BOOKING_POS_COST_UNIT],
            static::BOOKING_POS_COST_OBJECT_POSITION => $position[static::BOOKING_POS_COST_OBJECT],
            static::BOOKING_POS_COST_TYPE_POSITION => $position[static::BOOKING_POS_COST_TYPE],
            static::BOOKING_POS_PROJECT_POSITION => $position[static::BOOKING_POS_PROJECT],
            static::BOOKING_POS_TAX_TYPE_POSITION => $position[static::BOOKING_POS_TAX_TYPE],
            static::BOOKING_POS_CUSTOMS_DUTY_KEY_POSITION => $position[static::BOOKING_POS_CUSTOMS_DUTY_KEY],
            static::BOOKING_POS_AMOUNT_CURRENCY_NET_POSITION => $position[static::BOOKING_POS_AMOUNT_CURRENCY_NET],
            static::BOOKING_POS_AMOUNT_NET_POSITION => $position[static::BOOKING_POS_AMOUNT_NET],
            static::BOOKING_POS_TAX_AMOUNT_CURRENCY_POSITION => $position[static::BOOKING_POS_TAX_AMOUNT_CURRENCY],
            static::BOOKING_POS_TAX_AMOUNT_POSITION => $position[static::BOOKING_POS_TAX_AMOUNT],
            static::BOOKING_POS_QUANTITY_UNIT_POSITION => $position[static::BOOKING_POS_QUANTITY_UNIT],
            static::BOOKING_POS_QUANTITY_POSITION => $position[static::BOOKING_POS_QUANTITY],
            static::BOOKING_POS_AMOUNT_DISCOUNT_CURRENCY_POSITION => $position[static::BOOKING_POS_AMOUNT_DISCOUNT_CURRENCY],
            static::BOOKING_POS_AMOUNT_DISCOUNT_POSITION => $position[static::BOOKING_POS_AMOUNT_DISCOUNT]
        ];
    }
}
