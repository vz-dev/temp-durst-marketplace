<?php
/**
 * Durst - project - HttpRequestFacade.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 11:42
 */

namespace Pyz\Zed\HttpRequest\Business;


use Generated\Shared\Transfer\HttpRequestEntityTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class HttpRequestFacade
 * @package Pyz\Zed\HttpRequest\Business
 * @method HttpRequestBusinessFactory getFactory()
 */
class HttpRequestFacade extends AbstractFacade implements HttpRequestFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param int $idHttpRequest
     * @return \Generated\Shared\Transfer\HttpRequestEntityTransfer|null
     */
    public function getHttpRequestById(int $idHttpRequest): ?HttpRequestEntityTransfer
    {
        return $this
            ->getFactory()
            ->createHttpRequestModel()
            ->getHttpRequestById($idHttpRequest);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @param \Generated\Shared\Transfer\HttpResponseTransfer $responseTransfer
     * @return \Generated\Shared\Transfer\HttpRequestEntityTransfer
     */
    public function createHttpRequestLogEntry(
        HttpRequestTransfer $requestTransfer,
        HttpResponseTransfer $responseTransfer
    ): HttpRequestEntityTransfer
    {
        return $this
            ->getFactory()
            ->createHttpRequestModel()
            ->createHttpRequestLogEntry(
                $requestTransfer,
                $responseTransfer
            );
    }
}
