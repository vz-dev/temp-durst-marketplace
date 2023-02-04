<?php
/**
 * Durst - project - HttpRequestFacadeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 11:42
 */

namespace Pyz\Zed\HttpRequest\Business;


use Generated\Shared\Transfer\HttpRequestEntityTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;

interface HttpRequestFacadeInterface
{
    /**
     * Get a HTTP Request transfer by its ID
     * Return null if no given request is found
     *
     * @param int $idHttpRequest
     * @return \Generated\Shared\Transfer\HttpRequestEntityTransfer|null
     */
    public function getHttpRequestById(int $idHttpRequest): ? HttpRequestEntityTransfer;

    /**
     * Create a log entry in DB from request and response
     *
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @param \Generated\Shared\Transfer\HttpResponseTransfer $responseTransfer
     * @return \Generated\Shared\Transfer\HttpRequestEntityTransfer
     */
    public function createHttpRequestLogEntry(HttpRequestTransfer $requestTransfer, HttpResponseTransfer $responseTransfer): HttpRequestEntityTransfer;
}
