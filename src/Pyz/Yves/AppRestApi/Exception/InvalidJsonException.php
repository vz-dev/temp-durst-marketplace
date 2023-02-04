<?php
/**
 * Durst - project - InvalidJsonException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.04.18
 * Time: 17:13
 */

namespace Pyz\Yves\AppRestApi\Exception;


class InvalidJsonException extends JsonMalformedException
{
    const ERROR_CODE = 'INVALID_JSON';
    const MESSAGE = 'The request content is no valid JSON (missing a comma maybe ;)).';
}
