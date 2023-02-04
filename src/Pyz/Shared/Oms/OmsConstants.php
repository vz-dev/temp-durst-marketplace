<?php
/**
 * Durst - project - OmsConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.07.18
 * Time: 10:40
 */

namespace Pyz\Shared\Oms;

use Spryker\Shared\Oms\OmsConstants as SprykerOmsConstants;

interface OmsConstants extends SprykerOmsConstants
{
    public const OMS_RETAIL_ACCEPTED_STATE = 'OMS_RETAIL_ACCEPTED_STATE';
    public const OMS_WHOLESALE_ACCEPTED_STATE = 'OMS_WHOLESALE_ACCEPTED_STATE';

    public const OMS_RETAIL_DELIVERED_STATE = 'OMS_RETAIL_DELIVERED_STATE';
    public const OMS_WHOLESALE_PAYMENT_COMPLETE_STATES = 'OMS_WHOLESALE_PAYMENT_COMPLETE_STATES';

    public const OMS_WHOLESALE_PROCESS_NAME = 'OMS_WHOLESALE_PROCESS_NAME';
    public const OLD_PROCESSES_WHOLESALE_ORDER = 'OLD_PROCESSES_WHOLESALE_ORDER';

    public const ENVIRONMENT_PREFIX = 'ENVIRONMENT_PREFIX';

    public const INVOICE_PREFIX = 'INVOICE_PREFIX';
    public const NAME_INVOICE_REFERENCE = 'InvoiceReference';

    public const SALES_ORDER_RETRY_COUNTER = 'SALES_ORDER_RETRY_COUNTER';

    public const OMS_ERROR_MAIL_RECIPIENTS = 'OMS_ERROR_MAIL_RECIPIENTS';

    public const OMS_ERROR_MAIL_SUBJECT = 'OMS_ERROR_MAIL_SUBJECT';

    public const EVENTS_TO_SKIP = [
        'startCancel'
    ];

    public const RETAIL_PROCESS_NAME = 'RETAIL_PROCESS_NAME';
}
