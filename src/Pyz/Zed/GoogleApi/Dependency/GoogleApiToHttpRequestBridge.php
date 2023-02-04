<?php
/**
 * Durst - project - GoogleApiToHttpRequestBridge.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-07
 * Time: 13:03
 */

namespace Pyz\Zed\GoogleApi\Dependency;


use Generated\Shared\Transfer\HttpRequestEntityTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Pyz\Zed\HttpRequest\Business\HttpRequestFacadeInterface;

class GoogleApiToHttpRequestBridge implements GoogleApiToHttpRequestBridgeInterface
{
    /**
     * @var \Pyz\Zed\HttpRequest\Business\HttpRequestFacadeInterface
     */
    protected $httpRequestFacade;

    /**
     * GoogleApiToHttpRequestBridge constructor.
     * @param HttpRequestFacadeInterface $httpRequestFacade
     */
    public function __construct(
        HttpRequestFacadeInterface $httpRequestFacade
    )
    {
        $this->httpRequestFacade = $httpRequestFacade;
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
            ->httpRequestFacade
            ->createHttpRequestLogEntry(
                $requestTransfer,
                $responseTransfer
            );
    }
}
