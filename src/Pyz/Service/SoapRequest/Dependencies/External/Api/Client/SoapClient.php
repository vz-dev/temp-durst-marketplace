<?php
/**
 * Durst - project - SoapClient.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 26.10.20
 * Time: 16:16
 */

namespace Pyz\Service\SoapRequest\Dependencies\External\Api\Client;

use Generated\Shared\Transfer\SoapRequestErrorTransfer;
use Generated\Shared\Transfer\SoapRequestTransfer;
use Generated\Shared\Transfer\SoapResponseTransfer;
use Pyz\Service\SoapRequest\SoapRequestConfig;
use SoapClient as SoapClientSoapClient;
use SoapFault;
use stdClass;

class SoapClient implements SoapClientInterface
{
    /**
     * @var \Pyz\Service\SoapRequest\SoapRequestConfig
     */
    protected $config;

    /**
     * @var \Generated\Shared\Transfer\SoapRequestErrorTransfer
     */
    protected $error;

    /**
     * @var string
     */
    protected $wsdl;

    /**
     * SoapClient constructor.
     *
     * @param \Pyz\Service\SoapRequest\SoapRequestConfig $config
     */
    public function __construct(SoapRequestConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\SoapRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\SoapResponseTransfer
     */
    public function sendRequest(SoapRequestTransfer $requestTransfer): SoapResponseTransfer
    {
        $requestTransfer
            ->requireService()
            ->requireFunction();

        $soapClient = new SoapClientSoapClient(
            $requestTransfer->getWsdlUrl(),
            [
                'trace' => 1,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
            ]
        );

        try {
            $response = $soapClient->__soapCall($requestTransfer->getFunction(), $this->getParamsFromRequestTransfer($requestTransfer));

            $this
                ->addRequestBodyAndHeadersToTransfer($requestTransfer, $soapClient);

            return $this
                ->createResponseTransfer($response, $soapClient);
        } catch (SoapFault $soapFault) {
            $this->addError($soapFault);
        }

        return $this
            ->createResponseTransfer(new stdClass(), $soapClient);
    }

    /**
     * @param \SoapFault $soapFault
     *
     * @return void
     */
    protected function addError(SoapFault $soapFault): void
    {
        $error = (new SoapRequestErrorTransfer())
            ->setCode($soapFault->getCode())
            ->setMessage($soapFault->getMessage())
            ->setFile($soapFault->getFile())
            ->setLine($soapFault->getLine());

        $this->error = $error;
    }

    /**
     * @param \stdClass $response
     * @param \SoapClient|null $soapClient
     *
     * @return \Generated\Shared\Transfer\SoapResponseTransfer
     */
    protected function createResponseTransfer(stdClass $response, $soapClient = null): SoapResponseTransfer
    {
        $responseTransfer = new SoapResponseTransfer();

        $responseTransfer
            ->setData(
                $this
                    ->getBodyArrayFromResponse($response)
            )->setError(
                $this
                    ->error
            );

        if ($soapClient !== null) {
            $responseTransfer
                ->setCode(
                    $this
                        ->getHttpCodeFromHeaderString($soapClient->__getLastResponseHeaders())
                )
                ->setHeaders(
                    $soapClient->__getLastResponseHeaders()
                )
                ->setXml(
                    $soapClient->__getLastResponse()
                );
        }

        return $responseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\SoapRequestTransfer $soapRequestTransfer
     *
     * @return string
     */
    protected function getFunctionFromRequestTransfer(SoapRequestTransfer $soapRequestTransfer) : string
    {
        return $soapRequestTransfer
            ->getFunction();
    }

    /**
     * @param \Generated\Shared\Transfer\SoapRequestTransfer $soapRequestTransfer
     *
     * @return array
     */
    protected function getParamsFromRequestTransfer(SoapRequestTransfer $soapRequestTransfer) : array
    {
        $params = [];

        return array_merge($params, $soapRequestTransfer->getArgs());
    }

    /**
     * @param \stdClass $response
     *
     * @return array
     */
    protected function getBodyArrayFromResponse(stdClass $response) : array
    {
        return json_decode(json_encode($response), true);
    }

    /**
     * @param string $header
     *
     * @return int|null
     */
    protected function getHttpCodeFromHeaderString(string $header) : ?int
    {
        preg_match("/HTTP\/\d\.\d\s*\K[\d]+/", $header, $matches);

        if (count($matches) > 0) {
            return (int)($matches[0]);
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\SoapRequestTransfer $requestTransfer
     * @param \SoapClient $soapClient
     *
     * @return void
     */
    protected function addRequestBodyAndHeadersToTransfer(SoapRequestTransfer $requestTransfer, $soapClient)
    {
        $requestTransfer
            ->setXml($soapClient->__getLastRequest())
            ->setHeaders($soapClient->__getLastRequestHeaders());
    }
}
