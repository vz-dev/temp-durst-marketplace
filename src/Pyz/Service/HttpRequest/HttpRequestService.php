<?php
/**
 * Durst - project - HttpRequestService.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 11:34
 */

namespace Pyz\Service\HttpRequest;

use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Spryker\Service\Kernel\AbstractService;

/**
 * Class HttpRequestService
 * @package Pyz\Service\HttpRequest
 * @method HttpRequestServiceFactory getFactory()
 */
class HttpRequestService extends AbstractService implements HttpRequestServiceInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\HttpResponseTransfer
     */
    public function sendRequest(HttpRequestTransfer $requestTransfer): HttpResponseTransfer
    {
        return $this
            ->getFactory()
            ->createHttpClient()
            ->sendRequest($requestTransfer);
    }
}
