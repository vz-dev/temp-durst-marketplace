<?php
/**
 * Durst - project - EasybillConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.12.19
 * Time: 17:36
 */

namespace Pyz\Shared\Easybill;


use Pyz\Shared\HttpRequest\HttpRequestConstants;

interface EasybillConstants
{
    // the name of the sequence must be unique for each merchant
    public const INVOICE_SEQUENCE_NAME_FORMAT = 'INVOICE-%s';

    // name of queue for invoices that were rejected by the api due to too many requests per minute
    public const INVOICE_DELAY_QUEUE = 'invoice-delay';
    public const INVOICE_DELAY_ERROR_QUEUE = 'invoice-delay.error';

    // http status codes defined by the api
    public const CODE_SUCCESS = 201;
    public const CODE_INVALID_CUSTOMER = 400;
    public const CODE_RESOURCE_NOT_FOUND = 404;
    public const CODE_TOO_MANY_REQUEST = 429;

    // valid verbs used by the api
    public const VALID_HTTP_VERBS = [
        HttpRequestConstants::HTTP_VERB_PUT,
        HttpRequestConstants::HTTP_VERB_GET,
        HttpRequestConstants::HTTP_VERB_POST,
    ];

    // api date format
    public const DATE_FORMAT = 'dd.MM.YYYY';

    // enums defined by the api
    public const DOCUMENT_TYPE_INVOICE = 'INVOICE';
    public const DOCUMENT_POSITION_ITEM_TYPE_UNDEFINED = 'UNDEFINED';
    public const DOCUMENT_POSITION_TYPE_POSITION = 'POSITION';

}
