<?php
/**
 * Durst - project - SoapRequestFacade.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-02
 * Time: 15:47
 */

namespace Pyz\Zed\SoapRequest\Business;


use Generated\Shared\Transfer\SoapRequestEntityTransfer;
use Generated\Shared\Transfer\SoapRequestTransfer;
use Generated\Shared\Transfer\SoapResponseTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class SoapRequestFacade
 * @package Pyz\Zed\SoapRequest\Business
 * @method SoapRequestBusinessFactory getFactory()
 */
class SoapRequestFacade extends AbstractFacade implements SoapRequestFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param int $idSoapRequest
     * @return SoapRequestEntityTransfer|null
     */
    public function getSoapRequestById(int $idSoapRequest): ? SoapRequestEntityTransfer
    {
        return $this
            ->getFactory()
            ->createSoapRequestModel()
            ->getSoapRequestById($idSoapRequest);
    }

    /**
     * {@inheritDoc}
     *
     * @param SoapRequestTransfer $requestTransfer
     * @param SoapResponseTransfer $responseTransfer
     * @return SoapRequestEntityTransfer
     */
    public function createSoapRequestLogEntry(SoapRequestTransfer $requestTransfer, SoapResponseTransfer $responseTransfer): SoapRequestEntityTransfer
    {
        return $this
            ->getFactory()
            ->createSoapRequestModel()
            ->createSoapRequestLogEntry($requestTransfer, $responseTransfer);
    }
}
