<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 10:55
 */

namespace Pyz\Zed\Edifact\Business\Log;


use Generated\Shared\Transfer\EdifactExportLogTransfer;
use Orm\Zed\Edifact\Persistence\DstEdifactExportLog;
use Orm\Zed\Edifact\Persistence\Map\DstEdifactExportLogTableMap;
use Propel\Runtime\Exception\PropelException;
use Psr\Http\Message\ResponseInterface;
use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\Edifact\Business\Exception\InvalidExportTypeException;
use Pyz\Zed\Edifact\Persistence\EdifactQueryContainerInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class Logger implements LoggerInterface
{
    protected const UTF8_CONVERT = [
        'EdifactMessage',
        'EdifactErrorMessage',
        'ReasonPhrase'
    ];

    /**
     * @var EdifactQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var TourFacadeInterface
     */
    protected $tourFacade;

    /**
     * @var GraphMastersFacadeInterface
     */
    protected $graphMastersFacade;

    /**
     * Logger constructor.
     * @param EdifactQueryContainerInterface $queryContainer
     * @param TourFacadeInterface $tourFacade
     * @param GraphMastersFacadeInterface $graphMastersFacade
     */
    public function __construct(
        EdifactQueryContainerInterface $queryContainer,
        TourFacadeInterface $tourFacade,
        GraphMastersFacadeInterface $graphMastersFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->tourFacade = $tourFacade;
        $this->graphMastersFacade = $graphMastersFacade;
    }

    /**
     * {@inheritdoc}
     *
     * @param EdifactExportLogTransfer $edifactExportLgTransfer
     * @return void
     * @throws PropelException
     */
    public function log(EdifactExportLogTransfer $edifactExportLgTransfer)
    {
        $edifactExportLogEntity = $this
            ->transferToEntity($edifactExportLgTransfer);

        $edifactExportLogEntity
            ->save();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return bool
     * @throws AmbiguousComparisonException
     */
    public function areGoodsExportedSuccessfully(int $idConcreteTour): bool
    {
        return $this
            ->doesSuccessfulMessageEntryExistForType($idConcreteTour, EdifactConstants::EDIFACT_EXPORT_TYPE_ORDER);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return bool
     * @throws AmbiguousComparisonException
     */
    public function areDepositsExportedSuccessfully(int $idConcreteTour): bool
    {
        return $this
            ->doesSuccessfulMessageEntryExistForType($idConcreteTour, EdifactConstants::EDIFACT_EXPORT_TYPE_DEPOSIT);
    }

    /**
     * @param int $idConcreteTour
     * @param string $exportType
     * @return bool
     * @throws AmbiguousComparisonException
     */
    protected function doesSuccessfulMessageEntryExistForType(
        int $idConcreteTour,
        string $exportType
    ): bool
    {
        $entryCount = $this
            ->queryContainer
            ->queryEdifactExportLog()
            ->filterByExportType($exportType)
            ->filterByFkConcreteTour($idConcreteTour)
            ->filterByReasonPhrase(EdifactConstants::MESSAGE_SUCCESS)
            ->filterByStatusCode(EdifactConstants::STATUS_CODE_SUCCESS)
            ->count();

        return ($entryCount > 0);
    }

    /**
     * @param EdifactExportLogTransfer $edifactExportLogTransfer
     */
    protected function assertRequirements(EdifactExportLogTransfer $edifactExportLogTransfer)
    {
        $edifactExportLogTransfer
            ->requireEndpointUrl()
            ->requireExportType()
            ->requireReasonPhrase()
            ->requireStatusCode();
    }

    /**
     * @param EdifactExportLogTransfer $edifactExportLogTransfer
     * @return DstEdifactExportLog
     * @throws PropelException
     */
    protected function transferToEntity(EdifactExportLogTransfer $edifactExportLogTransfer): DstEdifactExportLog
    {
        $this
            ->assertRequirements($edifactExportLogTransfer);

        $edifactExportLogTransfer = $this
            ->encodeUtf8(
                $edifactExportLogTransfer
            );

        $edifactExportLogEntity = new DstEdifactExportLog();

        if ($edifactExportLogTransfer->getFkConcreteTour() !== null) {
            $edifactExportLogEntity->setFkConcreteTour($edifactExportLogTransfer->getFkConcreteTour());
        }

        if ($edifactExportLogTransfer->getFkGraphmastersTour() !== null) {
            $edifactExportLogEntity->setFkGraphmastersTour($edifactExportLogTransfer->getFkGraphmastersTour());
        }

        $edifactExportLogEntity
            ->setEdifactMessage($edifactExportLogTransfer->getEdifactMessage())
            ->setEdifactErrorMessage($edifactExportLogTransfer->getEdifactErrorMessage())
            ->setReasonPhrase($edifactExportLogTransfer->getReasonPhrase())
            ->setStatusCode($edifactExportLogTransfer->getStatusCode())
            ->setEndpointUrl($edifactExportLogTransfer->getEndpointUrl())
            ->setExportType($edifactExportLogTransfer->getExportType())
            ->setCreatedAt($edifactExportLogTransfer->getCreatedAt())
            ->setLogLevel($edifactExportLogTransfer->getLogLevel());

        return $edifactExportLogEntity;
    }

    /**
     * @param DstEdifactExportLog $dstEdifactExportLog
     * @return EdifactExportLogTransfer
     */
    protected function entityToTransfer(DstEdifactExportLog $dstEdifactExportLog): EdifactExportLogTransfer
    {
        return (new EdifactExportLogTransfer())
            ->fromArray(
                $dstEdifactExportLog->toArray(),
                true
            );
    }

    /**
     * {@inheritdoc}
     *
     * @param string $tourReference
     * @param string $endpointUrl
     * @param ResponseInterface $response
     * @param string|null $edifactMessage
     * @param string $logLevel
     * @param bool $isGraphmastersTour
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function logOrder(
        string $tourReference,
        string $endpointUrl,
        ResponseInterface $response,
        ?string $edifactMessage,
        string $logLevel,
        bool $isGraphmastersTour = false
    ) {
        $edifactLogTransfer = (new EdifactExportLogTransfer());

        if ($isGraphmastersTour === true) {
            $graphmastersTourTransfer = $this
                ->graphMastersFacade
                ->getTourByReference($tourReference);

            $edifactLogTransfer->setFkGraphmastersTour($graphmastersTourTransfer->getIdGraphmastersTour());
        } else {
            $concreteTourTransfer = $this
                ->tourFacade
                ->getConcreteTourByTourReference($tourReference);

            $edifactLogTransfer->setFkConcreteTour($concreteTourTransfer->getIdConcreteTour());
        }

        $edifactLogTransfer
            ->setExportType(EdifactConstants::EDIFACT_EXPORT_TYPE_ORDER)
            ->setEndpointUrl($endpointUrl)
            ->setStatusCode($response->getStatusCode())
            ->setReasonPhrase($response->getReasonPhrase())
            ->setEdifactMessage($edifactMessage)
            ->setEdifactErrorMessage($response->getBody()->getContents())
            ->setLogLevel($logLevel);

        $this
            ->log($edifactLogTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $tourReference
     * @param string $endpointUrl
     * @param ResponseInterface $response
     * @param string|null $edifactMessage
     * @param string $logLevel
     * @param bool $isGraphmastersTour
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function logDeposit(
        string $tourReference,
        string $endpointUrl,
        ResponseInterface $response,
        ?string $edifactMessage,
        string $logLevel,
        bool $isGraphmastersTour = false
    ) {
        $edifactLogTransfer = (new EdifactExportLogTransfer());

        if ($isGraphmastersTour === true) {
            $graphmastersTourTransfer = $this
                ->graphMastersFacade
                ->getTourByReference($tourReference);

            $edifactLogTransfer->setFkGraphmastersTour($graphmastersTourTransfer->getIdGraphmastersTour());
        } else {
            $concreteTourTransfer = $this
                ->tourFacade
                ->getConcreteTourByTourReference($tourReference);

            $edifactLogTransfer->setFkConcreteTour($concreteTourTransfer->getIdConcreteTour());
        }

        $edifactLogTransfer->setExportType(EdifactConstants::EDIFACT_EXPORT_TYPE_DEPOSIT)
            ->setEndpointUrl($endpointUrl)
            ->setStatusCode($response->getStatusCode())
            ->setReasonPhrase($response->getReasonPhrase())
            ->setEdifactMessage($edifactMessage)
            ->setEdifactErrorMessage($response->getBody()->getContents())
            ->setLogLevel($logLevel);

        $this
            ->log($edifactLogTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $endpointUrl
     * @param int $statusCode
     * @param string $reasonPhrase
     * @param string $logLevel
     * @param string|null $error
     * @return void
     * @throws PropelException
     */
    public function logNonEdi(
        string $endpointUrl,
        int $statusCode,
        string $reasonPhrase,
        string $logLevel,
        ?string $error = null
    )
    {
        $edifactLogTransfer = (new EdifactExportLogTransfer())
            ->setExportType(EdifactConstants::EDIFACT_EXPORT_TYPE_NON_EDI)
            ->setEndpointUrl($endpointUrl)
            ->setStatusCode($statusCode)
            ->setReasonPhrase($reasonPhrase)
            ->setLogLevel($logLevel);

        $this
            ->log($edifactLogTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $tourReference
     * @param string $endpointUrl
     * @param string $exportType
     * @param bool $isGraphmastersTour
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function logSuccessfulMessageTransfer(
        string $tourReference,
        string $endpointUrl,
        string $exportType,
        bool $isGraphmastersTour = false
    ): void {
        $this->assertExportTypeIsValid($exportType);

        $edifactLogTransfer = (new EdifactExportLogTransfer());

        if ($isGraphmastersTour === true) {
            $graphmastersTourTransfer = $this
                ->graphMastersFacade
                ->getTourByReference($tourReference);

            $edifactLogTransfer->setFkGraphmastersTour($graphmastersTourTransfer->getIdGraphmastersTour());
        } else {
            $concreteTourTransfer = $this
                ->tourFacade
                ->getConcreteTourByTourReference($tourReference);

            $edifactLogTransfer->setFkConcreteTour($concreteTourTransfer->getIdConcreteTour());
        }

        $edifactLogTransfer
            ->setExportType($exportType)
            ->setEndpointUrl($endpointUrl)
            ->setStatusCode(EdifactConstants::STATUS_CODE_SUCCESS)
            ->setReasonPhrase(EdifactConstants::MESSAGE_SUCCESS)
            ->setLogLevel(DstEdifactExportLogTableMap::COL_LOG_LEVEL_INFO);

        $this
            ->log($edifactLogTransfer);
    }

    /**
     * @param string $exportType
     * @throws InvalidExportTypeException
     */
    protected function assertExportTypeIsValid(string $exportType): void
    {
        if($exportType !== EdifactConstants::EDIFACT_EXPORT_TYPE_ORDER &&
            $exportType !== EdifactConstants::EDIFACT_EXPORT_TYPE_DEPOSIT &&
            $exportType !== EdifactConstants::EDIFACT_EXPORT_TYPE_NON_EDI){
            throw InvalidExportTypeException::invalidWithType($exportType);
        }
    }

    /**
     * @param EdifactExportLogTransfer $edifactExportLogTransfer
     * @return EdifactExportLogTransfer
     */
    protected function encodeUtf8(EdifactExportLogTransfer $edifactExportLogTransfer): EdifactExportLogTransfer
    {
        foreach (static::UTF8_CONVERT as $method) {
            $getter = sprintf(
                '%s%s',
                'get',
                $method
            );
            $setter = sprintf(
                '%s%s',
                'set',
                $method
            );

            if (
                method_exists($edifactExportLogTransfer, $getter) &&
                method_exists($edifactExportLogTransfer, $setter)
            ) {
                $value = $this
                    ->checkEncoding(
                        $edifactExportLogTransfer
                            ->{$getter}()
                    );

                $edifactExportLogTransfer
                    ->{$setter}(
                        $value
                    );
            }
        }

        return $edifactExportLogTransfer;
    }

    /**
     * @param string|null $original
     * @return string|null
     */
    protected function checkEncoding(?string $original): ?string
    {
        if ($original === null) {
            return null;
        }

        if (mb_detect_encoding($original, 'UTF-8', true) !== 'UTF-8') {
            return utf8_encode($original);
        }

        return $original;
    }
}
