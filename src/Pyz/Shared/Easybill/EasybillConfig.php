<?php
/**
 * Durst - project - EasybillConfig.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.12.19
 * Time: 17:02
 */

namespace Pyz\Shared\Easybill;


interface EasybillConfig
{
    // Information used to access the easybill api
    public const EASYBILL_API_URL = 'EASYBILL_API_URL';
    public const EASYBILL_API_KEY = 'EASYBILL_API_KEY';
    public const EASYBILL_EMAIL = 'EASYBILL_EMAIL';

    public const INVOICE_DELAY_QUEUE_CHUNK_SIZE = 'INVOICE_DELAY_QUEUE_CHUNK_SIZE';
}
