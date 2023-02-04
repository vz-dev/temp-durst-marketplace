<?php
/**
 * Durst - project - SoapRequestInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-02
 * Time: 15:50
 */

namespace Pyz\Zed\SoapRequest\Business\Model;


use Generated\Shared\Transfer\SoapRequestEntityTransfer;
use Generated\Shared\Transfer\SoapRequestTransfer;
use Generated\Shared\Transfer\SoapResponseTransfer;

interface SoapRequestInterface
{
    /**
     * @param int $idSoapRequest
     * @return SoapRequestEntityTransfer|null
     */
    public function getSoapRequestById(int $idSoapRequest): ?SoapRequestEntityTransfer;

    /**
     * @param SoapRequestTransfer $requestTransfer
     * @param SoapResponseTransfer $responseTransfer
     * @return SoapRequestEntityTransfer
     */
    public function createSoapRequestLogEntry(SoapRequestTransfer $requestTransfer, SoapResponseTransfer $responseTransfer): SoapRequestEntityTransfer;
}
