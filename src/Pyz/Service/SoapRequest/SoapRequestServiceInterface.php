<?php
/**
 * Durst - project - SoapRequestServiceInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.10.20
 * Time: 14:20
 */

namespace Pyz\Service\SoapRequest;


use Generated\Shared\Transfer\SoapRequestTransfer;
use Generated\Shared\Transfer\SoapResponseTransfer;

interface SoapRequestServiceInterface
{
    /**
     * @link https://ichhabdurst.atlassian.net/wiki/spaces/~366382927/pages/1500610561/Durst+SOAP-Modul
     *
     * @param \Generated\Shared\Transfer\SoapRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\SoapResponseTransfer
     */
    public function sendRequest(SoapRequestTransfer $requestTransfer): SoapResponseTransfer;
}
