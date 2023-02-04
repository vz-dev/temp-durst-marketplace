<?php
/**
 * Durst - project - HttpRequestServiceInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 11:33
 */

namespace Pyz\Service\HttpRequest;


use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;

interface HttpRequestServiceInterface
{
    /**
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\HttpResponseTransfer
     */
    public function sendRequest(HttpRequestTransfer $requestTransfer): HttpResponseTransfer;
}
