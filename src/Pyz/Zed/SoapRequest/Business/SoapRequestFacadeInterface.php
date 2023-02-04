<?php
/**
 * Durst - project - SoapRequestFacadeInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-02
 * Time: 15:46
 */

namespace Pyz\Zed\SoapRequest\Business;


use Generated\Shared\Transfer\SoapRequestEntityTransfer;
use Generated\Shared\Transfer\SoapRequestTransfer;
use Generated\Shared\Transfer\SoapResponseTransfer;

interface SoapRequestFacadeInterface
{
    /**
     * Get a SOAP-Request transfer by its ID
     * Return null if no given request is found
     *
     * @param int $idSoapRequest
     * @return SoapRequestEntityTransfer|null
     */
    public function getSoapRequestById(int $idSoapRequest): ? SoapRequestEntityTransfer;

    /**
     * Create a log entry in DB from request and response
     *
     * @param SoapRequestTransfer $requestTransfer
     * @param SoapResponseTransfer $responseTransfer
     * @return SoapRequestEntityTransfer
     */
    public function createSoapRequestLogEntry(SoapRequestTransfer $requestTransfer, SoapResponseTransfer $responseTransfer): SoapRequestEntityTransfer;
}
