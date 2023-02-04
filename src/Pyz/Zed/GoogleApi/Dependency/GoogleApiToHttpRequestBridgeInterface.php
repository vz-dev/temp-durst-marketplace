<?php
/**
 * Durst - project - GoogleApiToHttpRequestBridgeInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-07
 * Time: 13:00
 */

namespace Pyz\Zed\GoogleApi\Dependency;


use Generated\Shared\Transfer\HttpRequestEntityTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;

interface GoogleApiToHttpRequestBridgeInterface
{
    /**
     * Create a log entry in DB from request and response
     *
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @param \Generated\Shared\Transfer\HttpResponseTransfer $responseTransfer
     * @return \Generated\Shared\Transfer\HttpRequestEntityTransfer
     */
    public function createHttpRequestLogEntry(
        HttpRequestTransfer $requestTransfer,
        HttpResponseTransfer $responseTransfer
    ): HttpRequestEntityTransfer;
}
