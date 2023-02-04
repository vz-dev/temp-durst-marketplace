<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 11:16
 */

namespace Pyz\Zed\Edifact\Business;


use Propel\Runtime\Exception\PropelException;
use Psr\Http\Message\ResponseInterface;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * Class EdifactFacade
 * @package Pyz\Zed\Edifact\Business
 * @method EdifactBusinessFactory getFactory()
 */
class EdifactFacade extends AbstractFacade implements EdifactFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string $tourReference
     * @param string $endpointUrl
     * @param ResponseInterface $response
     * @param string|null $edifactMessage
     * @param string $logLevel
     * @param bool $isGraphmastersTour
     * @return void
     * @throws ContainerKeyNotFoundException
     */
    public function logOrder(
        string $tourReference,
        string $endpointUrl,
        ResponseInterface $response,
        ?string $edifactMessage,
        string $logLevel,
        bool $isGraphmastersTour = false
    ) {
        $this
            ->getFactory()
            ->createEdifactExportLogger()
            ->logOrder(
                $tourReference,
                $endpointUrl,
                $response,
                $edifactMessage,
                $logLevel,
                $isGraphmastersTour
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
     * @return void
     * @throws ContainerKeyNotFoundException
     */
    public function logDeposit(
        string $tourReference,
        string $endpointUrl,
        ResponseInterface $response,
        ?string $edifactMessage,
        string $logLevel,
        bool $isGraphmastersTour = false
    ) {
        $this
            ->getFactory()
            ->createEdifactExportLogger()
            ->logDeposit(
                $tourReference,
                $endpointUrl,
                $response,
                $edifactMessage,
                $logLevel,
                $isGraphmastersTour
            );
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
     * @throws ContainerKeyNotFoundException
     */
    public function logNonEdi(string $endpointUrl, int $statusCode, string $reasonPhrase, string $logLevel, ?string $error = null)
    {
        $this
            ->getFactory()
            ->createEdifactExportLogger()
            ->logNonEdi(
                $endpointUrl,
                $statusCode,
                $reasonPhrase,
                $logLevel,
                $error
            );
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
    ): void
    {
        $this
            ->getFactory()
            ->createEdifactExportLogger()
            ->logSuccessfulMessageTransfer($tourReference, $endpointUrl, $exportType, $isGraphmastersTour);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return bool
     * @throws ContainerKeyNotFoundException
     * @throws AmbiguousComparisonException
     */
    public function areGoodsExportedSuccessfully(int $idConcreteTour): bool
    {
        return $this
            ->getFactory()
            ->createEdifactExportLogger()
            ->areGoodsExportedSuccessfully($idConcreteTour);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return bool
     * @throws ContainerKeyNotFoundException
     * @throws AmbiguousComparisonException
     */
    public function areDepositsExportedSuccessfully(int $idConcreteTour): bool
    {
        return $this
            ->getFactory()
            ->createEdifactExportLogger()
            ->areDepositsExportedSuccessfully($idConcreteTour);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idTour
     * @param bool $isGraphmastersTour
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function setExportVersionForTour(int $idTour, bool $isGraphmastersTour = false): void
    {
        $this
            ->getFactory()
            ->getEdifactExportVersionConfig()
            ->setExportVersionForTour($idTour, $isGraphmastersTour);
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     * @throws ContainerKeyNotFoundException
     */
    public function getExportVersion(): string
    {
        return $this
            ->getFactory()
            ->getEdifactExportVersionConfig()
            ->getExportVersion();
    }
}
