<?php
/**
 * Durst - project - CancelOrderInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 17:05
 */

namespace Pyz\Zed\CancelOrder\Business\Model;

use DateTime;
use Generated\Shared\Transfer\CancelOrderTransfer;
use Generated\Shared\Transfer\JwtTransfer;

/**
 * Interface CancelOrderInterface
 * @package Pyz\Zed\CancelOrder\Business\Model
 */
interface CancelOrderInterface
{
    /**
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function generateToken(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer;

    /**
     * @param int $idSalesOrder
     * @param string|null $issuer
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function generateTokenForIssuer(
        int $idSalesOrder,
        ?string $issuer = null,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer;

    /**
     * @param string $token
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function getJwtFromToken(
        string $token
    ): JwtTransfer;

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return bool
     */
    public function isValid(
        JwtTransfer $jwtTransfer
    ): bool;

    /**
     * @param string|null $token
     * @param string|null $sign
     * @return bool
     */
    public function verifySignByToken(
        ?string $token = null,
        ?string $sign = null
    ): bool;

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return void
     */
    public function checkTransfer(
        JwtTransfer $jwtTransfer
    ): void;

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function executeJwtValidators(
        JwtTransfer $jwtTransfer
    ): JwtTransfer;

    /**
     * @param string $token
     * @return bool
     */
    public function saveCancelOrder(
        string $token
    ): bool;

    /**
     * @param int $idCancelOrder
     * @return \Generated\Shared\Transfer\CancelOrderTransfer
     */
    public function getCancelOrderById(
        int $idCancelOrder
    ): CancelOrderTransfer;

    /**
     * @param int $idSalesOrder
     * @return \Generated\Shared\Transfer\CancelOrderTransfer|null
     */
    public function getCancelOrderByIdSalesOrder(
        int $idSalesOrder
    ): ?CancelOrderTransfer;

    /**
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return string|null
     */
    public function getTokenForCustomerMail(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): ?string;

    /**
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function getJwtTransferForFridge(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer;

    /**
     * @param int $idSalesOrder
     * @param \DateTime|null $manualExpireDate
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function getJwtTransferForDriver(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer;
}
