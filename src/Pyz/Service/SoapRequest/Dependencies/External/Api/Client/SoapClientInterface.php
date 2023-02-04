<?php
/**
 * Durst - project - SoapClientInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 26.10.20
 * Time: 16:16
 */

namespace Pyz\Service\SoapRequest\Dependencies\External\Api\Client;


use Generated\Shared\Transfer\SoapRequestTransfer;
use Generated\Shared\Transfer\SoapResponseTransfer;

interface SoapClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\SoapRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\SoapResponseTransfer
     */
    public function sendRequest(SoapRequestTransfer $requestTransfer): SoapResponseTransfer;
}
