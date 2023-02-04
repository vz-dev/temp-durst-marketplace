<?php
/**
 * Durst - project - HttpClient.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 10:40
 */

namespace Pyz\Service\HttpRequest\Dependencies\External\Api\Client;

use ArrayObject;
use Generated\Shared\Transfer\HttpRequestErrorTransfer;
use Generated\Shared\Transfer\HttpRequestTransfer;
use Generated\Shared\Transfer\HttpResponseTransfer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Pyz\Service\HttpRequest\Dependencies\External\Api\Client\HandlerStack\HandlerStackContainer;
use Pyz\Service\HttpRequest\Dependencies\External\Api\Exception\HttpException\InvalidHttpMethodException as HttpRequestInvalidHttpMethodException;

class HttpClient implements HttpClientInterface
{
    public const SUPPORTED_METHODS = [
        'POST',
        'GET',
    ];

    protected const HTTP_VERSION = '1.1';

    /**
     * @var ArrayObject|\Generated\Shared\Transfer\HttpRequestErrorTransfer[]
     */
    protected $errors;

    /**
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\HttpResponseTransfer
     */
    public function sendRequest(HttpRequestTransfer $requestTransfer): HttpResponseTransfer
    {
        $this->errors = new ArrayObject();

        $this
            ->checkHttpMethod($requestTransfer);

        $options = $this
            ->createRequestOptions($requestTransfer);
        $request = $this
            ->createRequest($requestTransfer);

        try {
            $response = $this
                ->getClient($requestTransfer)
                ->send(
                    $request,
                    $options
                );
        } catch (GuzzleRequestException $exception) {
            $message = $exception
                ->getMessage();
            $response = $exception
                ->getResponse();

            if ($response !== null) {
                $message .= PHP_EOL .
                    PHP_EOL .
                    $response->getBody();
            }

            $this
                ->addError(
                    $exception->getCode(),
                    $message
                );
        }

        return $this
            ->createResponseTransfer(
                $response
            );
    }

    /**
     * @param int $code
     * @param string $message
     * @return void
     */
    protected function addError(int $code, string $message): void
    {
        $error = (new HttpRequestErrorTransfer())
            ->setCode($code)
            ->setMessage($message);

        $this->errors->append($error);
    }

    /**
     * @param \GuzzleHttp\Psr7\Response $response
     * @return \Generated\Shared\Transfer\HttpResponseTransfer
     */
    protected function createResponseTransfer(Response $response): HttpResponseTransfer
    {
        $httpResponseTransfer = new HttpResponseTransfer();

        $httpResponseTransfer
            ->setCode($response->getStatusCode())
            ->setCodeMessage($response->getReasonPhrase())
            ->setHeaders($response->getHeaders())
            ->setBody($response->getBody()->getContents())
            ->setErrors($this->errors);

        return $httpResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function createRequest(HttpRequestTransfer $requestTransfer): Request
    {
        return new Request(
            $requestTransfer->requireMethod()->getMethod(),
            $requestTransfer->requireUri()->getUri(),
            $requestTransfer->getHeaders(),
            null,
            self::HTTP_VERSION
        );
    }

    /**
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @return array
     */
    protected function createRequestOptions(HttpRequestTransfer $requestTransfer): array
    {
        return $this
            ->optionsToArray($requestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @return void
     */
    protected function checkHttpMethod(HttpRequestTransfer $requestTransfer): void
    {
        if (in_array(strtoupper($requestTransfer->getMethod()), self::SUPPORTED_METHODS) !== true) {
            throw new HttpRequestInvalidHttpMethodException($requestTransfer->getMethod());
        }
    }

    /**
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @return \GuzzleHttp\Client
     */
    protected function getClient(HttpRequestTransfer $requestTransfer): Client
    {
        return new Client(
            $this->getClientConfiguration($requestTransfer)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @return array
     */
    protected function getClientConfiguration(HttpRequestTransfer $requestTransfer): array
    {
        $config = $this
            ->optionsToArray($requestTransfer);

        $config['handler'] = $this
            ->getHandlerStack();

        return $config;
    }

    /**
     * @return \GuzzleHttp\HandlerStack
     */
    protected function getHandlerStack(): HandlerStack
    {
        $handlerStackContainer = new HandlerStackContainer();

        return $handlerStackContainer
            ->getHandlerStack();
    }

    /**
     * @param \Generated\Shared\Transfer\HttpRequestTransfer $requestTransfer
     * @return array
     */
    protected function optionsToArray(HttpRequestTransfer $requestTransfer): array
    {
        $options = [];
        $httpOptions = $requestTransfer
            ->getOptions();

        if ($httpOptions->getAllowRedirect() === null || $httpOptions->getAllowRedirect()->getAllowRedirect() === false) {
            $options['allow_redirects'] = false;
        } else {
            $options['allow_redirects'] = [
                'max' => $httpOptions->getAllowRedirect()->getMax(),
                'strict' => $httpOptions->getAllowRedirect()->getStrict(),
                'referer' => $httpOptions->getAllowRedirect()->getReferer(),
                'protocols' => $httpOptions->getAllowRedirect()->getProtocols(),
                'on_redirect' => $httpOptions->getAllowRedirect()->getOnRedirect(),
                'track_redirects' => $httpOptions->getAllowRedirect()->getTrackRedirects()
            ];
        }

        if ($httpOptions->getAuth() === null) {
            $options['auth'] = null;
        } else {
            $options['auth'] = [
                $httpOptions->getAuth()->getUsername(),
                $httpOptions->getAuth()->getPassword(),
            ];
            if ($httpOptions->getAuth()->getAuthType() !== null) {
                $options['auth'][] = $httpOptions->getAuth()->getAuthType();
            }
        }

        $options['debug'] = (($httpOptions->getDebug() === null || $httpOptions->getDebug() === false) !== true);

        if ($httpOptions->getFormParams() !== null) {
            $options['form_params'] = $httpOptions->getFormParams();
        }

        if ($httpOptions->getHeaders() !== null) {
            $options['headers'] = $httpOptions->getHeaders();
        }

        if (count($httpOptions->getJson()) > 0) {
            $options['json'] = $httpOptions->getJson();
        }

        if ($httpOptions->getMultiparts() !== null && $httpOptions->getMultiparts()->count() > 0) {
            $options['multipart'] = $this->getMultiparts($httpOptions->getMultiparts());
        }

        if ($httpOptions->getQuery() !== null) {
            $options['query'] = $httpOptions->getQuery();
        }

        if ($httpOptions->getTimeout() !== null) {
            $options['timeout'] = $httpOptions->getTimeout();
        }

        if ($httpOptions->getVerify() !== null) {
            $options['verify'] = $httpOptions->getVerify();
        } else {
            $options['verify'] = false;
        }

        if ($httpOptions->getVersion() !== null) {
            $options['version'] = $httpOptions->getVersion();
        } else {
            $options['version'] = self::HTTP_VERSION;
        }

        return $options;
    }

    /**
     * @param iterable $multiparts
     *
     * @return array
     */
    protected function getMultiparts(iterable $multiparts): array
    {
        $parts = [];

        foreach ($multiparts as $multipart) {
            $parts[] = [
                'name' => $multipart->getName(),
                'contents' => $multipart->getContents(),
                'headers' => $multipart->getHeaders(),
                'filename' => $multipart->getFilename()
            ];
        }

        return $parts;
    }
}
