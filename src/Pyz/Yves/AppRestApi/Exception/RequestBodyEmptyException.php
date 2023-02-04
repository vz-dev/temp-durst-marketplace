<?php

namespace Pyz\Yves\AppRestApi\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class RequestBodyEmptyException extends Exception
{
    const ERROR_CODE = 'REQUEST_BODY_EMPTY';
    const MESSAGE = 'The body of your request does not contain any data.';
    const STATUS_CODE = Response::HTTP_BAD_REQUEST;
}
