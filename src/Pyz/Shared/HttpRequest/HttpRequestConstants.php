<?php
/**
 * Durst - project - HttpRequestConfig.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 11:39
 */

namespace Pyz\Shared\HttpRequest;


use GuzzleHttp\RequestOptions;

interface HttpRequestConstants
{
    public const HTTP_REQUEST_TIMEOUT = 0;
    public const HTTP_CONNECT_TIMEOUT = 0;

    public const HTTP_VERB_POST = 'POST';
    public const HTTP_VERB_GET = 'GET';
    public const HTTP_VERB_PUT = 'PUT';

    public const HTTP_HEADER_AUTH = RequestOptions::AUTH;
}
