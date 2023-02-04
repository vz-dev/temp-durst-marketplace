<?php
/**
 * Durst - project - CustomerToSoapRequestInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.11.20
 * Time: 10:31
 */

namespace Pyz\Zed\Customer\Dependency\Facade;


use Generated\Shared\Transfer\SoapRequestEntityTransfer;
use Generated\Shared\Transfer\SoapRequestTransfer;
use Generated\Shared\Transfer\SoapResponseTransfer;

interface CustomerToSoapRequestInterface
{
    /**
     * @param SoapRequestTransfer $requestTransfer
     * @param SoapResponseTransfer $responseTransfer
     * @return SoapRequestEntityTransfer
     */
    public function createSoapRequestLogEntry(
        SoapRequestTransfer $requestTransfer,
        SoapResponseTransfer $responseTransfer
    ): SoapRequestEntityTransfer;
}
