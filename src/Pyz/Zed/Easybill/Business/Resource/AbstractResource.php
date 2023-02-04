<?php
/**
 * Durst - project - AbstractResource.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.01.20
 * Time: 16:44
 */

namespace Pyz\Zed\Easybill\Business\Resource;

use Generated\Shared\Transfer\HttpRequestAuthTransfer;
use Generated\Shared\Transfer\HttpRequestOptionsTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Pyz\Shared\Easybill\EasybillConstants;
use Pyz\Zed\Easybill\Business\Exception\EasybillException;
use Pyz\Zed\Easybill\Business\Exception\TooManyRequestException;
use Pyz\Zed\Easybill\Dependency\Service\EasybillToHttpRequestBridgeInterface;
use Pyz\Zed\Easybill\EasybillConfig;

abstract class AbstractResource
{
    protected const KEY_ID = 'id';

    /**
     * @var \Pyz\Zed\Easybill\Dependency\Service\EasybillToHttpRequestBridgeInterface
     */
    protected $httpRequestService;

    /**
     * @var \Pyz\Zed\Easybill\EasybillConfig
     */
    protected $config;

    /**
     * AbstractResource constructor.
     *
     * @param \Pyz\Zed\Easybill\Dependency\Service\EasybillToHttpRequestBridgeInterface $httpRequestService
     * @param \Pyz\Zed\Easybill\EasybillConfig $config
     */
    public function __construct(
        EasybillToHttpRequestBridgeInterface $httpRequestService,
        EasybillConfig $config
    ) {
        $this->httpRequestService = $httpRequestService;
        $this->config = $config;
    }

    /**
     * @param array $body
     * @param string $httpVerb
     * @param string $url
     *
     * @throws \Generated\Shared\Transfer\HttpRequestTransfer
     *
     * @return \Generated\Shared\Transfer\HttpRequestTransfer
     */
    protected function createHttpRequestTransfer(array $body, string $httpVerb, string $url): HttpRequestTransfer
    {
        if (in_array($httpVerb, EasybillConstants::VALID_HTTP_VERBS) !== true) {
            throw EasybillException::invalidHttpVerb($httpVerb);
        }

        return (new HttpRequestTransfer())
            ->setMethod($httpVerb)
            ->setUri($url)
            ->setOptions($this->createHttpOptionsTransfer($body));
    }

    /**
     * @param \Generated\Shared\Transfer\HttpResponseTransfer $httpResponseTransfer
     *
     * @return void
     */
    protected function checkResponseCode(HttpResponseTransfer $httpResponseTransfer): void
    {
        if ($httpResponseTransfer->getCode() === EasybillConstants::CODE_TOO_MANY_REQUEST) {
            throw TooManyRequestException::build();
        }
        if ($httpResponseTransfer->getCode() !== EasybillConstants::CODE_SUCCESS) {
            throw EasybillException::nonSuccessfulResponseCode($httpResponseTransfer->getCode());
        }
    }

    /**
     * @param array $body
     *
     * @return void
     */
    protected function checkBody(array $body): void
    {
        if (array_key_exists(static::KEY_ID, $body) !== true) {
            throw EasybillException::noIdInBody();
        }
    }

    /**
     * @param array $body
     * @return HttpRequestOptionsTransfer
     */
    protected function createHttpOptionsTransfer(array $body): HttpRequestOptionsTransfer
    {
        return (new HttpRequestOptionsTransfer())
            ->setJson($body)
            ->setAuth((new HttpRequestAuthTransfer())
                ->setUsername($this->config->getEasybillEmail())
                ->setPassword($this->config->getEasybillApiKey()));
    }
}
