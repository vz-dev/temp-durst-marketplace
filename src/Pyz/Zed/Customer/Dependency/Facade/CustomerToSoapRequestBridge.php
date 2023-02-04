<?php
/**
 * Durst - project - CustomerToSoapRequestBridge.php.
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
use Pyz\Zed\SoapRequest\Business\SoapRequestFacadeInterface;

class CustomerToSoapRequestBridge implements CustomerToSoapRequestInterface
{
    /**
     * @var SoapRequestFacadeInterface
     */
    protected $soapRequestFacade;

    /**
     * CustomerToSoapRequestBridge constructor.
     * @param SoapRequestFacadeInterface $soapRequestFacade
     */
    public function __construct(
        SoapRequestFacadeInterface $soapRequestFacade
    )
    {
        $this->soapRequestFacade = $soapRequestFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param SoapRequestTransfer $requestTransfer
     * @param SoapResponseTransfer $responseTransfer
     * @return SoapRequestEntityTransfer
     */
    public function createSoapRequestLogEntry(
        SoapRequestTransfer $requestTransfer,
        SoapResponseTransfer $responseTransfer
    ): SoapRequestEntityTransfer
    {
        return $this
            ->soapRequestFacade
            ->createSoapRequestLogEntry(
                $requestTransfer,
                $responseTransfer
            );
    }
}
