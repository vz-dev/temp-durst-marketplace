<?php
/**
 * Durst - project - CancelOrderFacade.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 16:48
 */

namespace Pyz\Zed\CancelOrder\Business;

use DateTime;
use Generated\Shared\Transfer\CancelOrderTransfer;
use Generated\Shared\Transfer\JwtTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class CancelOrderFacade
 * @package Pyz\Zed\CancelOrder\Business
 *
 * @method CancelOrderBusinessFactory getFactory()
 */
class CancelOrderFacade extends AbstractFacade implements CancelOrderFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function generateToken(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->generateToken(
                $idSalesOrder,
                $manualExpireDate
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param string|null $issuer
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function generateTokenForIssuer(
        int $idSalesOrder,
        ?string $issuer = null,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->generateTokenForIssuer(
                $idSalesOrder,
                $issuer,
                $manualExpireDate
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     * @return \Generated\Shared\Transfer\JwtTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getJwtFromToken(
        string $token
    ): JwtTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->getJwtFromToken(
                $token
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function isValid(
        JwtTransfer $jwtTransfer
    ): bool
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->isValid(
                $jwtTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function checkTransfer(
        JwtTransfer $jwtTransfer
    ): void
    {
        $this
            ->getFactory()
            ->createCancelOrderModel()
            ->checkTransfer(
                $jwtTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Generated\Shared\Transfer\JwtTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function executeJwtValidators(
        JwtTransfer $jwtTransfer
    ): JwtTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->executeJwtValidators(
                $jwtTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string|null $token
     * @param string|null $sign
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function verifySignByToken(
        ?string $token = null,
        ?string $sign = null
    ): bool
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->verifySignByToken(
                $token,
                $sign
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function saveCancelOrder(
        string $token
    ): bool
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->saveCancelOrder(
                $token
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCancelOrder
     * @return \Generated\Shared\Transfer\CancelOrderTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCancelOrderById(
        int $idCancelOrder
    ): CancelOrderTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->getCancelOrderById(
                $idCancelOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return \Generated\Shared\Transfer\CancelOrderTransfer|null
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getCancelOrderByIdSalesOrder(
        int $idSalesOrder
    ): ?CancelOrderTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->getCancelOrderByIdSalesOrder(
                $idSalesOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return string|null
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getTokenForCustomerMail(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): ?string
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->getTokenForCustomerMail(
                $idSalesOrder,
                $manualExpireDate
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getJwtTransferForFridge(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->getJwtTransferForFridge(
                $idSalesOrder,
                $manualExpireDate
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getJwtTransferForDriver(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderModel()
            ->getJwtTransferForDriver(
                $idSalesOrder,
                $manualExpireDate
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string|null $token
     * @return \Generated\Shared\Transfer\JwtTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function prepareTriggerFromToken(
        ?string $token = null
    ): JwtTransfer
    {
        return $this
            ->getFactory()
            ->createCancelOrderManager()
            ->prepareTriggerFromToken(
                $token
            );
    }
}
