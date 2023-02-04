<?php
/**
 * Durst - project - HttpClientInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 10:40
 */

namespace Pyz\Service\HttpRequest\Dependencies\External\Api\Client;


use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;

interface HttpClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\HttpResponseTransfer
     */
    public function sendRequest(HttpRequestTransfer $requestTransfer): HttpResponseTransfer;
}
