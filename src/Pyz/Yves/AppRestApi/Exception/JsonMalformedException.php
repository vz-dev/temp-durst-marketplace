<?php
/**
 * Durst - project - JsonMalformedException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.04.18
 * Time: 10:16
 */

namespace Pyz\Yves\AppRestApi\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class JsonMalformedException extends Exception
{
    const STATUS_CODE = Response::HTTP_BAD_REQUEST;
    const ERROR_CODE = 'JSON_MALFORMED';
    const MESSAGE = 'The JSON you sent to the API was malformed. Please check your request data.';
}
