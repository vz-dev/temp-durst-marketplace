<?php
/**
 * Durst - project - EasybillToHttpRequestBridgeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.12.19
 * Time: 11:20
 */

namespace Pyz\Zed\Easybill\Dependency\Service;


use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;

interface EasybillToHttpRequestBridgeInterface
{
    /**
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $httpRequestTransfer
     *
     * @return \Generated\Shared\Transfer\HttpResponseTransfer
     */
    public function sendRequest(HttpRequestTransfer $httpRequestTransfer): HttpResponseTransfer;
}
