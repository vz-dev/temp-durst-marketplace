<?php
/**
 * Durst - project - SoapRequest.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-02
 * Time: 15:49
 */

namespace Pyz\Zed\SoapRequest\Business\Model;


use Generated\Shared\Transfer\HttpRequestEntityTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use Generated\Shared\Transfer\SoapRequestEntityTransfer;
use Generated\Shared\Transfer\SoapRequestTransfer;
use Generated\Shared\Transfer\SoapResponseTransfer;
use Orm\Zed\SoapRequest\Persistence\DstSoapRequest;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\SoapRequest\Persistence\SoapRequestQueryContainerInterface;

class SoapRequest implements SoapRequestInterface
{
    /**
     * @var SoapRequestQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * HttpRequest constructor.
     * @param SoapRequestQueryContainerInterface $queryContainer
     */
    public function __construct(
        SoapRequestQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param int $idSoapRequest
     * @return SoapRequestEntityTransfer|null
     */
    public function getSoapRequestById(int $idSoapRequest): ?SoapRequestEntityTransfer
    {
        $soapRequestEntity = $this
            ->queryContainer
            ->querySoapRequestById($idSoapRequest)
            ->findOne();

        if ($soapRequestEntity === null) {
            return null;
        }

        return $this
            ->entityToTransfer($soapRequestEntity);
    }

    /**
     * @param DstSoapRequest $request
     * @return SoapRequestEntityTransfer
     */
    protected function entityToTransfer(DstSoapRequest $request): SoapRequestEntityTransfer
    {
        $transfer = new SoapRequestEntityTransfer();

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
     * @param HttpRequestTransfer $requestTransfer
     * @param HttpResponseTransfer $responseTransfer
     * @return HttpRequestEntityTransfer
     * @throws PropelException
     */
    public function createSoapRequestLogEntry(
        SoapRequestTransfer $requestTransfer,
        SoapResponseTransfer $responseTransfer
    ): SoapRequestEntityTransfer
    {
        $soapEntity = new DstSoapRequest();

        $soapEntity
            ->setRequestService($requestTransfer->getService())
            ->setRequestFunction($requestTransfer->getFunction())
            ->setRequestHeaders($requestTransfer->getHeaders())
            ->setRequestXml($requestTransfer->getXml())
            ->setRequestArgs($this->getJsonEncodedOrNull($requestTransfer->getArgs()))
            ->setResponseCode($responseTransfer->getCode())
            ->setResponseHeaders($responseTransfer->getHeaders())
            ->setResponseXml($responseTransfer->getXml())
            ->setResponseData($this->getJsonEncodedOrNull($responseTransfer->getData()))
            ->setResponseError($this->getErrorFromResponse($responseTransfer));

        $soapEntity
            ->save();

        return $this
            ->entityToTransfer($soapEntity);
    }

    /**
     * @param SoapResponseTransfer $responseTransfer
     * @return false|string|null
     */
    protected function getErrorFromResponse(SoapResponseTransfer $responseTransfer)
    {
        if($responseTransfer->getError() !== null){
            return json_encode($responseTransfer->getError()->toArray());
        }

        return null;
    }

    /**
     * @param array|null $value
     * @return false|string|null
     */
    protected function getJsonEncodedOrNull(?array $value)
    {
        if($value === null || $value == "")
        {
            return null;
        }

        return json_encode($value);
    }
}
