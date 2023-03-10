<?php
/**
 * Durst - project - AccountingConstants.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 25.03.20
 * Time: 14:31
 */

namespace Pyz\Shared\Accounting;


interface AccountingConstants
{
    // Keys to fixed and variable license entries
    public const INVOICE_LICENSE_FIXED = 'INVOICE_LICENSE_FIXED';
    public const INVOICE_LICENSE_VARIABLE = 'INVOICE_LICENSE_VARIABLE';
    public const INVOICE_LICENSE_FIXED_REDUCED = 'INVOICE_LICENSE_FIXED_REDUCED';
    public const INVOICE_LICENSE_VARIABLE_REDUCED = 'INVOICE_LICENSE_VARIABLE_REDUCED';

    public const INVOICE_MARKETING_FIXED = 'INVOICE_MARKETING_FIXED';
    public const INVOICE_MARKETING_VARIABLE = 'INVOICE_MARKETING_VARIABLE';
    public const INVOICE_MARKETING_FIXED_REDUCED = 'INVOICE_MARKETING_FIXED_REDUCED';
    public const INVOICE_MARKETING_VARIABLE_REDUCED = 'INVOICE_MARKETING_VARIABLE_REDUCED';

    public const OMS_WHOLESALE_PAYMENT_ACCOUNTING_STATES = 'OMS_WHOLESALE_PAYMENT_ACCOUNTING_STATES';

    public const REALAX_HEADER_IDENTIFIER = 'HF';
    public const REALAX_HEADER_APPLICATION_NUMBER = 'DST';
    public const REALAX_HEADER_OEM_TO_ANSI = 1;
    public const REALAX_HEADER_CARRYOVER = 0;

    public const REALAX_HEAD_IDENTIFIER = 'HB';
    public const REALAX_HEAD_ACCOUNT_TYPE = 1;
    public const REALAX_HEAD_DEBITS = 1;
    public const REALAX_HEAD_BOOKING_TYPE_NUMBER = 1;
    public const REALAX_HEAD_CURRENCY = 'EUR';
    public const REALAX_HEAD_PAYMENT_TYPE = 1;
    public const REALAX_HEAD_PAYMENT_CONDITION = 1;
    public const REALAX_HEAD_AUTO_DUNNING = 0;
    public const REALAX_HEAD_AUTO_REGULATION = 0;

    public const REALAX_POSITION_IDENTIFIER = 'BP';
    public const REALAX_POSITION_ACCOUNT_TYPE = 3;
    public const REALAX_POSITION_DEBITS = 2;
    public const REALAX_POSITION_TAX_TYPE = 2;
    public const REALAX_POSITION_CUSTOMS_DUTY_KEY = 1;
    public const REALAX_POSITION_CUSTOMS_DUTY_KEY_REDUCED = 6;

    public const REALAX_DELIMITER = 'REALAX_DELIMITER';
    public const REALAX_CSV_LINE_FORMAT = 'REALAX_CSV_LINE_FORMAT';

    public const REALAX_EXPORT_PATH  = 'REALAX_EXPORT_PATH';

    public const REALAX_RECIPIENTS = 'REALAX_RECIPIENTS';

    public const PROCESS_TIMEOUT = 'PROCESS_TIMEOUT';

    public const REALAX_CORONA_TAX_REDUCTION_MONTH = 'REALAX_CORONA_TAX_REDUCTION_MONTH';
    public const REALAX_CORONA_TAX_REDUCTION_YEAR = 'REALAX_CORONA_TAX_REDUCTION_YEAR';

    public const REALAX_NORMAL_TAX_RATE = 'REALAX_NORMAL_TAX_RATE';
    public const REALAX_CORONA_TAX_RATE = 'REALAX_CORONA_TAX_RATE';
}
