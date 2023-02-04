<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-12-12
 * Time: 10:20
 */

namespace Pyz\Zed\Tour\Business\Client;


use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Pyz\Zed\Tour\TourConfig;
use function GuzzleHttp\Psr7\stream_for;
use function GuzzleHttp\Psr7\uri_for;
use GuzzleHttp\Psr7\UriResolver;
use GuzzleHttp\RequestOptions;
use Orm\Zed\Edifact\Persistence\Map\DstEdifactExportLogTableMap;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\Tour\Business\Exception\TourExportException;

class TourExportClient
{
    public const REQUEST_METHOD_POST = 'POST';
    public const GUZZLE_OPTION_BASE_URI = 'base_uri';

    /**
     * @var array
     */
    protected $options;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Pyz\Zed\Edifact\Business\EdifactFacadeInterface
     */
    protected $edifactLogger;

    /**
     * @var \Pyz\Zed\Tour\TourConfig
     */
    protected $config;

    /**
     * TourExportClient constructor.
     * @param string $uri
     * @param array $options
     * @param \Pyz\Zed\Edifact\Business\EdifactFacadeInterface $edifactLogger
     * @param \Pyz\Zed\Tour\TourConfig $config
     * @throws \Pyz\Zed\Tour\Business\Exception\TourExportException
     */
    public function __construct(
        string $uri,
        array $options,
        EdifactFacadeInterface $edifactLogger,
        TourConfig $config
    )
    {
        $options[self::GUZZLE_OPTION_BASE_URI] = $uri;
        $this->config = $config;
        $this->options = $this
            ->mergeOptions(
                $options
            );
        $this->edifactLogger = $edifactLogger;

        $this->initClient();
    }

    /**
     * @throws TourExportException
     * @return void
     */
    protected function initClient()
    {
        $uri = uri_for($this->options[self::GUZZLE_OPTION_BASE_URI]);

        if (strtolower($uri->getScheme()) !== 'https') {
            $this
                ->edifactLogger
                ->logNonEdi(
                    $this->options[self::GUZZLE_OPTION_BASE_URI],
                    500,
                    TourExportException::ERROR_NO_HTTPS_SCHEMA,
                    DstEdifactExportLogTableMap::COL_LOG_LEVEL_CRITICAL
                );

            throw new TourExportException(TourExportException::ERROR_NO_HTTPS_SCHEMA);
        }

        $this->client = new Client($this->options);

        $this
            ->edifactLogger
            ->logNonEdi(
                $this->options[self::GUZZLE_OPTION_BASE_URI],
                200,
                'Client created',
                DstEdifactExportLogTableMap::COL_LOG_LEVEL_INFO
            );
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string|null $username
     * @param string|null $password
     * @param string $body
     * @return PromiseInterface
     */
    public function requestAsync(string $method, string $uri, ?string $username, ?string $password, string $body): PromiseInterface
    {
        $this
            ->edifactLogger
            ->logNonEdi(
                $this->buildUri($uri),
                200,
                sprintf(
                    'Request (async) with data: %s',
                    $body
                ),
                DstEdifactExportLogTableMap::COL_LOG_LEVEL_INFO
            );

        $bodyStream = stream_for($body);

        $options = [
            RequestOptions::BODY => $bodyStream,
            RequestOptions::VERIFY => false
        ];

        if ($username !== null && $password !== null) {
            $options[RequestOptions::AUTH] = [
                $username,
                $password
            ];
        }

        return $this
            ->client
            ->requestAsync($method, $uri, $options);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string|null $username
     * @param string|null $password
     * @param string $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $method, string $uri, ?string $username, ?string $password, string $body)
    {
        $this
            ->edifactLogger
            ->logNonEdi(
                $this->buildUri($uri),
                200,
                sprintf(
                    'Request (sync) with data: %s',
                    $body
                ),
                DstEdifactExportLogTableMap::COL_LOG_LEVEL_INFO
            );

        $bodyStream = stream_for($body);

        $options = [
            RequestOptions::BODY => $bodyStream,
            RequestOptions::VERIFY => false
        ];

        if ($username !== null && $password !== null) {
            $options[RequestOptions::AUTH] = [
                $username,
                $password
            ];
        }

        return $this
            ->client
            ->request($method, $uri, $options);
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function buildUri(string $uri): string
    {
        $uri = uri_for($uri);
        if (isset($this->options[self::GUZZLE_OPTION_BASE_URI])) {
            $uri = UriResolver::resolve(uri_for($this->options[self::GUZZLE_OPTION_BASE_URI]), $uri);
        }

        if ($uri->getScheme() === '' && $uri->getHost() !== '') {
            return $uri->withScheme('http');
        }

        return  $uri;
    }

    /**
     * @param array $options
     * @return array
     */
    protected function mergeOptions(array $options): array
    {
        return array_merge_recursive(
            $options,
            $this
                ->config
                ->getEdiClientCurlOptions()
        );
    }
}
