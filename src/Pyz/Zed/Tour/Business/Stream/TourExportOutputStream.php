<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-11-27
 * Time: 15:41
 */

namespace Pyz\Zed\Tour\Business\Stream;

use GuzzleHttp\Exception\GuzzleException;
use Orm\Zed\Edifact\Persistence\Map\DstEdifactExportLogTableMap;
use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\Tour\Business\Client\TourExportClient;
use Pyz\Zed\Tour\Business\Exception\TourExportException;
use Pyz\Zed\Tour\Business\Parser\TourExportParser;
use Pyz\Zed\Tour\TourConfig;
use SprykerMiddleware\Shared\Process\Stream\StreamInterface;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Business\Exception\MethodNotSupportedException;

class TourExportOutputStream implements StreamInterface, WriteStreamInterface
{
    protected const EXPORT_TYPE = EdifactConstants::EDIFACT_EXPORT_TYPE_ORDER;

    protected const REASON_PHRASE = 'Export concrete tour';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $uploadName;

    /**
     * @var TourExportClient
     */
    protected $client;

    /**
     * @var TourExportParser
     */
    protected $parser;

    /**
     * @var EdifactFacadeInterface
     */
    protected $edifactLogger;

    /**
     * @var TourConfig
     */
    protected $config;

    /**
     * @var bool
     */
    protected $isGraphmastersTour;

    /**
     * TourExportOutputStream constructor.
     *
     * @param string $path
     * @param TourExportParser $tourExportParser
     * @param EdifactFacadeInterface $edifactLogger
     * @param TourConfig $config
     */
    public function __construct(
        string $path,
        TourExportParser $tourExportParser,
        EdifactFacadeInterface $edifactLogger,
        TourConfig $config
    ) {
        $this->path = $path;
        $this->parser = $tourExportParser;
        $this->edifactLogger = $edifactLogger;
        $this->config = $config;
        $this->isGraphmastersTour = false;
    }

    /**
     * @return bool
     */
    public function open(): bool
    {
        try {
            $this->client = new TourExportClient(
                $this->path,
                [],
                $this->edifactLogger,
                $this->config
            );

            return true;
        } catch (TourExportException $exception) {
            $this
                ->edifactLogger
                ->logNonEdi(
                    $this->path,
                    $exception->getCode(),
                    $exception->getMessage(),
                    DstEdifactExportLogTableMap::COL_LOG_LEVEL_ERROR
                );

            return false;
        }
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        try {
            $content = $this
                ->parser
                ->getParsedContent();

            $content = $this
                ->checkEncoding($content);

            if ($this->client !== null) {
                $response = $this
                    ->client
                    ->request(
                        TourExportClient::REQUEST_METHOD_POST,
                        '',
                        $this->parser->getBasicAuthUsername(),
                        $this->parser->getBasicAuthPassword(),
                        $content
                    );

                $logLevel = DstEdifactExportLogTableMap::COL_LOG_LEVEL_INFO;

                if ($this->isHttpError($response->getStatusCode()) === true) {
                    $logLevel = DstEdifactExportLogTableMap::COL_LOG_LEVEL_ERROR;
                }

                if ($this->parser->isDepositExport() === true) {
                    $this
                        ->edifactLogger
                        ->logDeposit(
                            $this->parser->getTourReference(),
                            $this->path,
                            $response,
                            $content,
                            $logLevel,
                            $this->isGraphmastersTour
                        );
                } else {
                    $this
                        ->edifactLogger
                        ->logOrder(
                            $this->parser->getTourReference(),
                            $this->path,
                            $response,
                            $content,
                            $logLevel,
                            $this->isGraphmastersTour
                        );
                }

                if ($this->isHttpError($response->getStatusCode()) === true) {
                    $this
                        ->edifactLogger
                        ->logNonEdi(
                            $this->path,
                            $response->getStatusCode(),
                            $response->getReasonPhrase(),
                            DstEdifactExportLogTableMap::COL_LOG_LEVEL_ERROR,
                            $response->getBody()->getContents()
                        );

                    return false;
                }

                $this
                    ->edifactLogger
                    ->logNonEdi(
                        $this->path,
                        $response->getStatusCode(),
                        static::REASON_PHRASE,
                        DstEdifactExportLogTableMap::COL_LOG_LEVEL_INFO
                    );

                $this
                    ->edifactLogger
                    ->logSuccessfulMessageTransfer(
                        $this->parser->getTourReference(),
                        $this->path,
                        static::EXPORT_TYPE,
                        $this->isGraphmastersTour
                    );

                return true;
            }
        } catch (GuzzleException $exception) {
            $error = $exception
                ->getTraceAsString();

            if (
                method_exists($exception, 'getResponse') &&
                method_exists($exception->getResponse(), 'getBody') &&
                method_exists($exception->getResponse()->getBody(), 'getContents')
            ) {
                $bodyContent = $exception
                    ->getResponse()
                    ->getBody()
                    ->getContents();

                if (!empty($bodyContent)) {
                    $error = $bodyContent;
                }
            }

            $this
                ->edifactLogger
                ->logNonEdi(
                    $this->path,
                    $exception->getCode(),
                    $exception->getMessage(),
                    DstEdifactExportLogTableMap::COL_LOG_LEVEL_ERROR,
                    $error
                );
        }

        return false;
    }

    /**
     * @param int $offset
     * @param int $whence
     *
     * @return int
     * @throws MethodNotSupportedException
     *
     */
    public function seek(int $offset, int $whence): int
    {
        throw new MethodNotSupportedException();
    }

    /**
     * @return bool
     * @throws MethodNotSupportedException
     *
     */
    public function eof(): bool
    {
        throw new MethodNotSupportedException();
    }

    /**
     * @param array $data
     *
     * @return int
     */
    public function write(array $data): int
    {
        $this
            ->parser
            ->addExportRow($data);

        return 1;
    }

    /**
     * @return bool
     */
    public function flush(): bool
    {
        return true;
    }

    /**
     * @param string $stringToCheck
     * @return string
     */
    protected function checkEncoding(string $stringToCheck): string
    {
        if(mb_detect_encoding($stringToCheck, 'UTF-8', true) !== 'UTF-8'){
            return utf8_encode($stringToCheck);
        }

        return $stringToCheck;
    }

    /**
     * @param int $statusCode
     *
     * @return bool
     */
    protected function isHttpError(int $statusCode): bool
    {
        if ($statusCode < 200 || $statusCode > 400) {
            return true;
        }

        return false;
    }
}
