<?php
/**
 * Durst - project - GraphhopperToHttpRequestBrdigeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 27.11.19
 * Time: 14:01
 */

namespace Pyz\Zed\Graphhopper\Dependency;


use Generated\Shared\Transfer\HttpRequestEntityTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;

interface GraphhopperToHttpRequestBridgeInterface
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
