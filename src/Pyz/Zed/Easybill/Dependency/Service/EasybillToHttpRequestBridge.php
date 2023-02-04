<?php
/**
 * Durst - project - EasybillToHttpRequestBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.12.19
 * Time: 11:20
 */

namespace Pyz\Zed\Easybill\Dependency\Service;

use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Pyz\Service\HttpRequest\HttpRequestServiceInterface;

class EasybillToHttpRequestBridge implements EasybillToHttpRequestBridgeInterface
{
    /**
     * @var \Pyz\Service\HttpRequest\HttpRequestServiceInterface
     */
    protected $httpRequestService;

    /**
     * EasybillToHttpRequestBridge constructor.
     *
     * @param \Pyz\Service\HttpRequest\HttpRequestServiceInterface $httpRequestService
     */
    public function __construct(HttpRequestServiceInterface $httpRequestService)
    {
        $this->httpRequestService = $httpRequestService;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $httpRequestTransfer
     *
     * @return \Generated\Shared\Transfer\HttpResponseTransfer
     */
    public function sendRequest(HttpRequestTransfer $httpRequestTransfer): HttpResponseTransfer
    {
        return $this
            ->httpRequestService
            ->sendRequest($httpRequestTransfer);
    }
}
