<?php
/**
 * Durst - project - GraphhopperToHttpRequestBrdige.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 27.11.19
 * Time: 14:02
 */

namespace Pyz\Zed\Graphhopper\Dependency;


use Generated\Shared\Transfer\HttpRequestEntityTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Pyz\Zed\HttpRequest\Business\HttpRequestFacadeInterface;

class GraphhopperToHttpRequestBridge implements GraphhopperToHttpRequestBridgeInterface
{
    /**
     * @var \Pyz\Zed\HttpRequest\Business\HttpRequestFacadeInterface
     */
    protected $httpRequestFacade;

    /**
     * GraphhopperToHttpRequestBridge constructor.
     * @param \Pyz\Zed\HttpRequest\Business\HttpRequestFacadeInterface $httpRequestFacade
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
