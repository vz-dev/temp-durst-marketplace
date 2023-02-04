<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 10:55
 */

namespace Pyz\Zed\Edifact\Business\Log;


use Generated\Shared\Transfer\EdifactExportLogTransfer;
use Propel\Runtime\Exception\PropelException;
use Psr\Http\Message\ResponseInterface;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface LoggerInterface
{
    /**
     * @param EdifactExportLogTransfer $edifactExportLogTransfer
     * @return void
     */
    public function log(EdifactExportLogTransfer $edifactExportLogTransfer);

    /**
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
     * @param string $endpointUrl
     * @param int $statusCode
     * @param string $reasonPhrase
     * @param string $logLevel
     * @param string|null $error
     * @return void
     */
    public function logNonEdi(string $endpointUrl, int $statusCode, string $reasonPhrase, string $logLevel, ?string $error = null);

    /**
     * @param string $tourReference
     * @param string $endpointUrl
     * @param string $exportType
     * @param  bool $isGraphmastersTour
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function logSuccessfulMessageTransfer(
        string $tourReference,
        string $endpointUrl,
        string $exportType,
        bool $isGraphmastersTour = false
    ): void;

    /**
     * @param int $idConcreteTour
     * @return bool
     * @throws AmbiguousComparisonException
     */
    public function areGoodsExportedSuccessfully(int $idConcreteTour): bool;

    /**
     * @param int $idConcreteTour
     * @return bool
     * @throws AmbiguousComparisonException
     */
    public function areDepositsExportedSuccessfully(int $idConcreteTour): bool;
}
