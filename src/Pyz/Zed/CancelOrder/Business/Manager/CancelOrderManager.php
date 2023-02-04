<?php
/**
 * Durst - project - CancelOrderManager.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.09.21
 * Time: 15:13
 */

namespace Pyz\Zed\CancelOrder\Business\Manager;

use Exception;
use Generated\Shared\Transfer\JwtTransfer;
use Pyz\Shared\CancelOrder\CancelOrderConstants;
use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;
use Pyz\Zed\CancelOrder\Business\Exception\CancelOrderTokenNotSetException;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;

/**
 * Class CancelOrderManager
 * @package Pyz\Zed\CancelOrder\Business\Manager
 */
class CancelOrderManager implements CancelOrderManagerInterface
{
    /**
     * @var \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface
     */
    protected $facade;

    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @param \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface $facade
     * @param \Pyz\Zed\Sales\Business\SalesFacadeInterface $salesFacade
     */
    public function __construct(
        CancelOrderFacadeInterface $facade,
        SalesFacadeInterface $salesFacade
    )
    {
        $this->facade = $facade;
        $this->salesFacade = $salesFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param string|null $token
     * @return \Generated\Shared\Transfer\JwtTransfer
     * @throws \Pyz\Zed\CancelOrder\Business\Exception\CancelOrderTokenNotSetException
     * @throws \Exception
     */
    public function prepareTriggerFromToken(
        ?string $token = null
    ): JwtTransfer
    {
        if ($token === null) {
            throw new CancelOrderTokenNotSetException(
                CancelOrderTokenNotSetException::MESSAGE
            );
        }

        // create JWT from token
        $jwtTransfer = $this
            ->facade
            ->getJwtFromToken(
                $token
            );

        // if the created JWT already has some errors, throw the first one out
        if ($jwtTransfer->getErrors()->count() > 0) {
            throw new Exception(
                $jwtTransfer
                    ->getErrors()
                    ->offsetGet(0)
                    ->getMessage()
            );
        }

        // run through the validations, basic and additional
        $jwtTransfer = $this
            ->facade
            ->executeJwtValidators(
                $jwtTransfer
            );

        // if the JWT already has some errors from validators, throw the first one out
        if ($jwtTransfer->getErrors()->count() > 0) {
            throw new Exception(
                $jwtTransfer
                    ->getErrors()
                    ->offsetGet(0)
                    ->getMessage()
            );
        }

        // more validation checks, throw error in case of failure
        $this
            ->facade
            ->checkTransfer(
                $jwtTransfer
            );

        // so far, no error and no exception
        $idSalesOrder = $jwtTransfer
            ->getId();

        $saleOrder = $this
            ->salesFacade
            ->getOrderByIdSalesOrder(
                $idSalesOrder
            );

        // update sales order cancel issuer now
        $issuer = $jwtTransfer
            ->getIssuer();
        if (
            $issuer !== null &&
            $saleOrder->getCancelIssuer() !== $issuer
        ) {
            $this
                ->salesFacade
                ->updateCancelIssuer(
                    $idSalesOrder,
                    $issuer
                );
        }

        // update sales order cancel message now
        $message = $this
            ->getCancelMessage(
                $jwtTransfer
            );
        if (
            $message !== null &&
            $saleOrder->getCancelMessage() !== $message
        ) {
            $this
                ->salesFacade
                ->updateCancelMessage(
                    $idSalesOrder,
                    $message
                );
        }

        // update sales order driver now
        $driver = $this
            ->getIdDriver(
                $jwtTransfer
            );
        if (
            $driver !== null &&
            $saleOrder->getFkDriver() !== $driver
        ) {
            $this
                ->salesFacade
                ->updateDriverForOrder(
                    $idSalesOrder,
                    $driver
                );
        }

        return $jwtTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return string|null
     */
    protected function getCancelMessage(JwtTransfer $jwtTransfer): ?string
    {
        $additionalParameters = $jwtTransfer
            ->getAdditionalParameters();

        foreach ($additionalParameters as $additionalParameter) {
            if ($additionalParameter->getKey() === CancelOrderConstants::KEY_MESSAGE) {
                return $additionalParameter
                    ->getValue();
            }
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return int|null
     */
    protected function getIdDriver(
        JwtTransfer $jwtTransfer
    ): ?int
    {
        $additionalParameters = $jwtTransfer
            ->getAdditionalParameters();

        foreach ($additionalParameters as $additionalParameter) {
            if (
                $additionalParameter->getKey() === CancelOrderConstants::KEY_ID_DRIVER &&
                $additionalParameter->getValue() !== null
            ) {
                return (int)$additionalParameter
                    ->getValue();
            }
        }

        return null;
    }
}
