<?php
/**
 * Durst - project - SoapRequestService.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.10.20
 * Time: 14:25
 */

namespace Pyz\Service\SoapRequest;


use Generated\Shared\Transfer\SoapRequestTransfer;
use Generated\Shared\Transfer\SoapResponseTransfer;
use Spryker\Service\Kernel\AbstractService;

/**
 * Class SoapRequestService
 * @package Pyz\Service\SoapRequest
 * @method \Pyz\Service\SoapRequest\SoapRequestServiceFactory getFactory()
 */
class SoapRequestService extends AbstractService implements SoapRequestServiceInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\SoapRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\SoapResponseTransfer
     */
    public function sendRequest(SoapRequestTransfer $requestTransfer): SoapResponseTransfer
    {
        return $this
            ->getFactory()
            ->createSoapClient()
            ->sendRequest(
                $requestTransfer
            );
    }
}
