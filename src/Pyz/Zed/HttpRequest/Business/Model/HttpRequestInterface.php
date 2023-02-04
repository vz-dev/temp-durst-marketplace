<?php
/**
 * Durst - project - HttpRequestInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.19
 * Time: 12:50
 */

namespace Pyz\Zed\HttpRequest\Business\Model;


use Generated\Shared\Transfer\HttpRequestEntityTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;

interface HttpRequestInterface
{
    /**
     * @param int $idHttpRequest
     * @return \Generated\Shared\Transfer\HttpRequestEntityTransfer|null
     */
    public function getHttpRequestById(int $idHttpRequest): ?HttpRequestEntityTransfer;

    /**
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @param \Generated\Shared\Transfer\HttpResponseTransfer $responseTransfer
     * @return \Generated\Shared\Transfer\HttpRequestEntityTransfer
     */
    public function createHttpRequestLogEntry(HttpRequestTransfer $requestTransfer, HttpResponseTransfer $responseTransfer): HttpRequestEntityTransfer;
}
