<?php
/**
 * Durst - project - HttpRequest.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.19
 * Time: 12:50
 */

namespace Pyz\Zed\HttpRequest\Business\Model;


use Generated\Shared\Transfer\HttpRequestEntityTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Orm\Zed\HttpRequest\Persistence\PyzHttpRequest;
use Pyz\Zed\HttpRequest\Persistence\HttpRequestQueryContainerInterface;

class HttpRequest implements HttpRequestInterface
{
    /**
     * @var \Pyz\Zed\HttpRequest\Persistence\HttpRequestQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * HttpRequest constructor.
     * @param \Pyz\Zed\HttpRequest\Persistence\HttpRequestQueryContainerInterface $queryContainer
     */
    public function __construct(
        HttpRequestQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param int $idHttpRequest
     * @return \Generated\Shared\Transfer\HttpRequestEntityTransfer|null
     */
    public function getHttpRequestById(int $idHttpRequest): ?HttpRequestEntityTransfer
    {
        $httpRequestEntity = $this
            ->queryContainer
            ->queryHttpRequestById($idHttpRequest)
            ->findOne();

        if ($httpRequestEntity === null) {
            return null;
        }

        return $this
            ->entityToTransfer($httpRequestEntity);
    }

    /**
     * @param \Orm\Zed\HttpRequest\Persistence\PyzHttpRequest $request
     * @return \Generated\Shared\Transfer\HttpRequestEntityTransfer
     */
    protected function entityToTransfer(PyzHttpRequest $request): HttpRequestEntityTransfer
    {
        $transfer = new HttpRequestEntityTransfer();

        $transfer
            ->fromArray(
                $request->toArray(),
                true
            );

        return $transfer;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @param \Generated\Shared\Transfer\HttpResponseTransfer $responseTransfer
     * @return \Generated\Shared\Transfer\HttpRequestEntityTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createHttpRequestLogEntry(
        HttpRequestTransfer $requestTransfer,
        HttpResponseTransfer $responseTransfer
    ): HttpRequestEntityTransfer
    {
        $httpEntity = new PyzHttpRequest();

        $httpEntity
            ->setRequestBody(json_encode($requestTransfer->getBody()))
            ->setRequestHeaders(json_encode($requestTransfer->getHeaders()))
            ->setRequestMethod($requestTransfer->getMethod())
            ->setRequestOptions(json_encode($requestTransfer->getOptions()->toArray()))
            ->setRequestTimeout($requestTransfer->getTimeout())
            ->setRequestUri($requestTransfer->getUri())
            ->setResponseBody(json_encode($responseTransfer->getBody()))
            ->setResponseCode($responseTransfer->getCode())
            ->setResponseHeaders(json_encode($responseTransfer->getHeaders()))
            ->setResponseMessage($responseTransfer->getCodeMessage())
            ->setResponseErrors(json_encode($responseTransfer->getErrors()));

        $httpEntity
            ->save();

        return $this
            ->entityToTransfer($httpEntity);
    }
}
