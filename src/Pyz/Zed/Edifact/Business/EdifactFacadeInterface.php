<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 11:15
 */

namespace Pyz\Zed\Edifact\Business;


use Psr\Http\Message\ResponseInterface;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

interface EdifactFacadeInterface
{
    /**
     * Log an EDIFact request / response of type ORDERS
     *
     * @param string $tourReference
     * @param string $endpointUrl
     * @param ResponseInterface $response
     * @param string|null $edifactMessage
     * @param string $logLevel
     * @param bool $isGraphmastersTour
     * @return void
     */
    public function logOrder(
        string $tourReference,
        string $endpointUrl,
        ResponseInterface $response,
        ?string $edifactMessage,
        string $logLevel,
        bool $isGraphmastersTour = false
    );

    /**
     * Log an EDIFact request / response of type DEPOSIT
     *
     * @param string $tourReference
     * @param string $endpointUrl
     * @param ResponseInterface $response
     * @param string|null $edifactMessage
     * @param string $logLevel
     * @param bool $isGraphmastersTour
     * @return void
     */
    public function logDeposit(
        string $tourReference,
        string $endpointUrl,
        ResponseInterface $response,
        ?string $edifactMessage,
        string $logLevel,
        bool $isGraphmastersTour = false
    );

    /**
     * Log an error thrown within classes used by EDI (e.g. Guzzle / cURL)
     *
     * @param string $endpointUrl
     * @param int $statusCode
     * @param string $reasonPhrase
     * @param string $logLevel
     * @param string|null $error
     * @return void
     */
    public function logNonEdi(string $endpointUrl, int $statusCode, string $reasonPhrase, string $logLevel, ?string $error = null);

    /**
     * Specification:
     *  - Checks whether there is one log entry matching the success status code and success message
     *  - Only returns true if one entry with the matching status code and message is found
     * @see \Pyz\Zed\Tour\Business\Client\TourExportClient::STATUS_CODE_SUCCESS
     * @see \Pyz\Zed\Tour\Business\Client\TourExportClient::MESSAGE_SUCCESS
     *
     * @param int $idConcreteTour
     * @return bool
     */
    public function areGoodsExportedSuccessfully(int $idConcreteTour): bool;

    /**
     * Specification:
     *  - Checks whether there are exactly two log entries matching the success status code and success message.
     *    The first entry belongs to the goods edi message and the second to the deposit edi message
     *  - Only returns true if one entry with the matching status code and message is found
     * @see \Pyz\Zed\Tour\Business\Client\TourExportClient::STATUS_CODE_SUCCESS
     * @see \Pyz\Zed\Tour\Business\Client\TourExportClient::MESSAGE_SUCCESS
     *
     * @param int $idConcreteTour
     * @return bool
     */
    public function areDepositsExportedSuccessfully(int $idConcreteTour): bool;

    /**
     * Specification:
     *  - Adds an entry to the log table related to the concrete tour with the given id
     *  - Sets status code and message to the following constants
     * @see \Pyz\Shared\Edifact\EdifactConstants::STATUS_CODE_SUCCESS
     * @see \Pyz\Shared\Edifact\EdifactConstants::MESSAGE_SUCCESS
     *
     * @param string $tourReference
     * @param string $endpointUrl
     * @param string $exportType
     * @param bool $isGraphmastersTour
     * @return void
     */
    public function logSuccessfulMessageTransfer(
        string $tourReference,
        string $endpointUrl,
        string $exportType,
        bool $isGraphmastersTour = false
    ): void;

    /**
     * Stores the EDIFACT export version for the concrete tour or Graphmasters tour with the given ID in the config
     *
     * @param int $idTour
     * @param bool $isGraphmastersTour
     * @throws ContainerKeyNotFoundException
     */
    public function setExportVersionForTour(int $idTour, bool $isGraphmastersTour = false): void;

    /**
     * Retrieves the currently set EDIFACT export version from the config
     *
     * @return string
     * @throws ContainerKeyNotFoundException
     */
    public function getExportVersion(): string;
}
